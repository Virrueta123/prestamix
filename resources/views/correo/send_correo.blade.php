<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html lang="en" dir="ltr" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="UTF-8" />
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta name="x-apple-disable-message-reformatting" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta content="telephone=no" name="format-detection" />
    <title>Horizon Finance</title>

    <style type="text/css">
        @import url('https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Nunito+Sans:ital,opsz,wght@0,6..12,200..1000;1,6..12,200..1000&display=swap');

        .lato-thin {
            font-family: "Lato", sans-serif;
            font-style: normal;
        }

        .button_a {
            padding: 8px;
            background: #2E3192;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 2px 2px 8px rgba(0, 0, 0, 0.4);
        }

        .btn-send-email {
            background: #2E3192;
            /* Color de fondo */
            color: white;
            /* Color del texto */
            border: none;
            /* Sin borde */
            padding: 12px 24px;
            /* Espacio interno */
            font-size: 16px;
            /* Tamaño de fuente */
            border-radius: 6px;
            /* Borde redondeado */
            cursor: pointer;
            /* Cursor de puntero */
            transition: background-color 0.3s ease;
            /* Transición suave */
        }
    </style>
</head>

<body class="body"
    style="width: 96%; height: 100%;border:2px solid #2E3192; padding: 10px; margin: 0; background:#F4F5FF; ">
    <center><img src="{{ asset('dist/images/logo/logo.png') }}" alt=""></center>
    <hr>
    <h2 class="lato-thin">{{ $titulo }}</h2>
    <p class="lato-thin">{{ $mensaje }}</p>

    @if ($url == 'N')
    @else
        <a href="{{ $url }}" class="lato-thin"><button class="btn-send-email">Ver mensaje</button></a>
    @endif
  
</body>

</html>
