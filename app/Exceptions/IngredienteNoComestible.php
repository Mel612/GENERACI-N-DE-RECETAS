<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * La IA decidió que lo que la persona escribió no es un alimento.
 *
 * Es un RuntimeException para que quien ya atrapa los errores del asistente
 * siga funcionando; el mensaje es el motivo que dio la IA.
 */
class IngredienteNoComestible extends RuntimeException {}
