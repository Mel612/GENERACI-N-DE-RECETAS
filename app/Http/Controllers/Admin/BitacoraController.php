<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bitacora;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Monitoreo del sistema (Requerimiento 20).
 *
 * La bitácora es de solo lectura: no se edita ni se borra desde la interfaz.
 * Un registro de auditoría que se puede alterar no sirve como registro.
 */
class BitacoraController extends Controller
{
    /** Cómo se lee cada acción y de qué color se muestra. */
    public const ACCIONES = [
        'inicio_sesion'    => ['Inicio de sesión',        'exito'],
        'cierre_sesion'    => ['Cierre de sesión',        'neutro'],
        'registro_usuario' => ['Registro de usuario',     'exito'],
        'crear'            => ['Creación',                'exito'],
        'editar'           => ['Modificación',            'oro'],
        'eliminar'         => ['Eliminación',             'error'],
        'desactivar'       => ['Desactivación',           'alerta'],
        'crear_usuario'    => ['Alta de usuario',         'exito'],
        'editar_usuario'   => ['Edición de usuario',      'oro'],
        'eliminar_usuario' => ['Baja de usuario',         'error'],
        'editar_perfil'    => ['Edición de perfil',       'oro'],
        'cambio_password'  => ['Cambio de contraseña',    'alerta'],
        'cambio_factor'    => ['Cambio de factor',        'alerta'],
    ];

    public function index(Request $request): View
    {
        $consulta = Bitacora::with('usuario');

        if ($request->filled('usuario')) {
            $consulta->where('cod_usuario_bit', $request->integer('usuario'));
        }

        if ($request->filled('accion')) {
            $consulta->where('acc_bit', $request->string('accion')->toString());
        }

        if ($request->filled('tabla')) {
            $consulta->where('tab_bit', $request->string('tabla')->toString());
        }

        if ($request->filled('desde')) {
            $consulta->whereDate('fec_cre_bit', '>=', $request->date('desde'));
        }

        if ($request->filled('hasta')) {
            $consulta->whereDate('fec_cre_bit', '<=', $request->date('hasta'));
        }

        return view('admin.bitacora.index', [
            'registros' => $consulta->orderByDesc('fec_cre_bit')->paginate(30)->withQueryString(),
            'usuarios'  => Usuario::orderBy('nom_usu')->get(['cod_usuario', 'nom_usu']),

            // Solo se ofrecen los valores que existen de verdad en la bitácora.
            'acciones'  => Bitacora::select('acc_bit')->distinct()->orderBy('acc_bit')->pluck('acc_bit'),
            'tablas'    => Bitacora::whereNotNull('tab_bit')->select('tab_bit')->distinct()->orderBy('tab_bit')->pluck('tab_bit'),

            'etiquetas' => self::ACCIONES,

            'resumen' => [
                'total'   => Bitacora::count(),
                'hoy'     => Bitacora::whereDate('fec_cre_bit', today())->count(),
                'semana'  => Bitacora::where('fec_cre_bit', '>=', now()->subDays(7))->count(),
                'activos' => Bitacora::where('fec_cre_bit', '>=', now()->subDays(7))
                                     ->distinct('cod_usuario_bit')->count('cod_usuario_bit'),
            ],

            'masFrecuentes' => Bitacora::select('acc_bit as etiqueta', DB::raw('COUNT(*) as valor'))
                ->groupBy('acc_bit')->orderByDesc('valor')->limit(8)->get()
                ->map(function ($f) {
                    $f->etiqueta = self::ACCIONES[$f->etiqueta][0] ?? $f->etiqueta;
                    return $f;
                }),
        ]);
    }
}
