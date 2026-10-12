{{--
    Correo del enlace de recuperación.

    Va con estilos en línea y tablas a propósito: los clientes de correo
    (Gmail, Outlook) descartan las hojas de estilo externas y muchos ni
    siquiera respetan flexbox. Aquí no sirve nada del sistema de diseño.
--}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Restablece tu contraseña</title>
</head>
<body style="margin:0; padding:0; background-color:#0e0a06; font-family:Arial, Helvetica, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0e0a06; padding:32px 16px;">
    <tr>
        <td align="center">

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:520px; background-color:#1a130b; border:1px solid rgba(212,160,23,.18); border-radius:16px;">

                <tr>
                    <td style="padding:32px 32px 0 32px;">
                        <p style="margin:0; font-size:22px; color:#f0c040; font-weight:bold;">
                            {{ config('app.name') }}
                        </p>
                    </td>
                </tr>

                <tr>
                    <td style="padding:24px 32px 0 32px;">
                        <p style="margin:0 0 16px 0; font-size:16px; color:#f5e6c8;">
                            Hola, {{ $usuario->nom_usu }}:
                        </p>

                        <p style="margin:0 0 16px 0; font-size:15px; line-height:1.6; color:#c9b894;">
                            Recibimos una solicitud para restablecer la contraseña de tu cuenta.
                            Usa el botón para elegir una nueva.
                        </p>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding:8px 32px 24px 32px;">
                        <a href="{{ $enlace }}"
                           style="display:inline-block; padding:14px 28px; background-color:#d4a017;
                                  color:#0e0a06; font-size:15px; font-weight:bold;
                                  text-decoration:none; border-radius:999px;">
                            Restablecer mi contraseña
                        </a>
                    </td>
                </tr>

                <tr>
                    <td style="padding:0 32px 24px 32px;">
                        <p style="margin:0 0 16px 0; font-size:13px; line-height:1.6; color:#8a7f6e;">
                            El enlace vence en {{ $minutos }} minutos y sirve una sola vez.
                        </p>

                        {{-- Quien no pidió nada tiene que saber que no debe hacer nada --}}
                        <p style="margin:0 0 16px 0; font-size:13px; line-height:1.6; color:#8a7f6e;">
                            Si tú no pediste esto, puedes ignorar este correo:
                            tu contraseña seguirá siendo la misma.
                        </p>

                        {{-- Algunos clientes no dejan hacer clic en el botón --}}
                        <p style="margin:0; font-size:12px; line-height:1.6; color:#8a7f6e;
                                  word-break:break-all; border-top:1px solid rgba(212,160,23,.12); padding-top:16px;">
                            Si el botón no funciona, copia esta dirección en tu navegador:<br>
                            <span style="color:#c9b894;">{{ $enlace }}</span>
                        </p>
                    </td>
                </tr>
            </table>

            <p style="margin:24px 0 0 0; font-size:12px; color:#8a7f6e;">
                {{ config('app.name') }} &middot; Sistema de generación de recetas
            </p>

        </td>
    </tr>
</table>

</body>
</html>
