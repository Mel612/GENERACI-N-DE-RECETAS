<?php

namespace App\Http\Controllers\Concerns;

use App\Services\AsistenteIA;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Reglas comunes para los nombres y descripciones de los catálogos.
 *
 * - Se limpian antes de validar: sin espacios de más y con mayúscula inicial.
 * - Solo letras, espacios, comas y guiones: nada de números ni símbolos.
 * - Sin la misma letra tres veces seguidas ("aaaa"), que delata texto basura.
 * - Únicos sin importar mayúsculas ni tildes: «lácteos» y «Lacteos» chocan.
 * - Opcionalmente, la IA revisa que el nombre tenga que ver con la cocina.
 */
trait ValidaNombres
{
    /** Patrón de un nombre válido: empieza con letra; luego letras, espacios, comas o guiones. */
    protected const PATRON_NOMBRE = '/^[\pL][\pL\s,\-]*$/u';

    /** Minúsculas y sin tildes, para comparar nombres. */
    protected function normalizar(string $texto): string
    {
        return Str::of($texto)->ascii()->lower()->squish()->toString();
    }

    /**
     * Quita espacios de más y pone mayúscula inicial en los campos indicados.
     * Un campo que queda vacío pasa a null.
     *
     * @param  array<int, string>  $campos
     */
    protected function limpiarTextos(Request $request, array $campos): void
    {
        $request->merge(collect($campos)->mapWithKeys(function (string $campo) use ($request) {
            $valor = Str::squish((string) $request->input($campo));

            return [$campo => $valor === '' ? null : Str::ucfirst($valor)];
        })->all());
    }

    /**
     * Reglas para el nombre de un registro de catálogo.
     *
     * @param  class-string<Model>  $modelo
     * @param  string  $que  Cómo se nombra en los mensajes, con artículo: "la categoría"
     */
    protected function reglasNombre(string $modelo, string $columna, ?int $id, string $que,
                                    int $minimo = 3, int $maximo = 60): array
    {
        return [
            'required', 'string', "min:{$minimo}", "max:{$maximo}",
            'regex:' . self::PATRON_NOMBRE,
            'not_regex:/(\pL)\1\1/iu',
            $this->reglaUnicoNormalizado($modelo, $columna, $id, $que),
        ];
    }

    /**
     * Falla si ya existe otro registro con el mismo texto en esa columna,
     * sin importar mayúsculas ni tildes.
     *
     * @param  class-string<Model>  $modelo
     */
    protected function reglaUnicoNormalizado(string $modelo, string $columna, ?int $id, string $que): Closure
    {
        return function (string $atributo, mixed $valor, Closure $fallar) use ($modelo, $columna, $id, $que) {
            $clave = (new $modelo)->getKeyName();

            $existentes = $modelo::query()
                ->when($id, fn ($q) => $q->where($clave, '!=', $id))
                ->pluck($columna);

            $repetido = $existentes->first(
                fn ($existente) => $this->normalizar((string) $existente) === $this->normalizar((string) $valor)
            );

            if ($repetido !== null) {
                $fallar(Str::ucfirst("{$que} «{$repetido}» ya existe. No se distinguen mayúsculas ni tildes."));

                return;
            }

            // Las abreviaturas son muy cortas para comparar singular y plural
            if (mb_strlen((string) $valor) < 4) {
                return;
            }

            $gemelo = $existentes->first(
                fn ($existente) => array_intersect($this->variantes((string) $existente), $this->variantes((string) $valor)) !== []
            );

            if ($gemelo !== null) {
                $fallar(Str::ucfirst("{$que} «{$gemelo}» ya existe: es lo mismo en singular o plural."));
            }
        };
    }

    /**
     * Formas de un nombre para reconocer singular y plural:
     * «Postres» → postres, postre; «Guarniciones» → guarniciones, guarnicion.
     *
     * @return array<int, string>
     */
    protected function variantes(string $nombre): array
    {
        $normal = $this->normalizar($nombre);

        return array_values(array_unique(array_filter([
            $normal,
            preg_replace('/s$/', '', $normal),
            preg_replace('/es$/', '', $normal),
        ], fn ($v) => mb_strlen($v) >= 3)));
    }

    /** Reglas para una descripción opcional, que no puede repetir el nombre. */
    protected function reglasDescripcion(Request $request, string $campoNombre, int $maximo = 255): array
    {
        $nombre = (string) $request->input($campoNombre);

        return [
            'nullable', 'string', 'min:5', "max:{$maximo}",
            'not_regex:/<[^>]*>/',
            function (string $atributo, mixed $valor, Closure $fallar) use ($nombre) {
                if ($this->normalizar((string) $valor) === $this->normalizar($nombre)) {
                    $fallar('La descripción no puede ser igual al nombre: explica qué incluye.');
                }
            },
        ];
    }

    /**
     * Mensajes en español para las reglas de nombre y descripción.
     *
     * @param  string  $que  Con artículo: "la categoría"
     * @return array<string, string>
     */
    protected function mensajesNombre(string $campo, string $que, ?string $campoDescripcion = null): array
    {
        $mensajes = [
            "{$campo}.required"  => Str::ucfirst("el nombre de {$que} es obligatorio."),
            "{$campo}.min"       => Str::ucfirst("el nombre de {$que} debe tener al menos :min letras."),
            "{$campo}.max"       => Str::ucfirst("el nombre de {$que} no puede pasar de :max caracteres."),
            "{$campo}.regex"     => Str::ucfirst("el nombre de {$que} solo puede llevar letras y espacios: nada de números ni símbolos."),
            "{$campo}.not_regex" => 'El nombre no puede repetir la misma letra tres veces seguidas.',
        ];

        if ($campoDescripcion) {
            $mensajes += [
                "{$campoDescripcion}.min"       => 'La descripción es muy corta: escribe al menos 5 caracteres o déjala vacía.',
                "{$campoDescripcion}.max"       => 'La descripción no puede pasar de :max caracteres.',
                "{$campoDescripcion}.not_regex" => 'La descripción no puede llevar etiquetas HTML.',
            ];
        }

        return $mensajes;
    }

    /**
     * Pide a la IA que confirme que el nombre tiene que ver con la cocina.
     *
     * Solo consulta si el nombre es nuevo o cambió, para no gastar el cupo
     * cada vez que se edita otra cosa del registro.
     *
     * @param  class-string<Model>  $modelo
     * @param  string  $queEs  "una categoría de ingredientes de cocina"
     *
     * @throws ValidationException
     */
    protected function comprobarConIa(string $campo, string $nombre, string $modelo, ?int $id, string $queEs): void
    {
        $anterior = $id ? $modelo::find($id)?->getAttribute($campo) : null;

        if ($anterior !== null && $this->normalizar($anterior) === $this->normalizar($nombre)) {
            return;
        }

        $motivo = app(AsistenteIA::class)->revisarNombreCatalogo(Auth::user(), $nombre, $queEs);

        if ($motivo !== null) {
            throw ValidationException::withMessages([
                $campo => "«{$nombre}» no se puede registrar como {$queEs}. {$motivo}",
            ]);
        }
    }
}
