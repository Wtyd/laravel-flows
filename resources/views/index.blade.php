<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <link rel="icon" href="/icons/favicon-32x32.png" sizes="32x32" />
  <link rel="apple-touch-icon-precomposed" href="/icons/favicon-180x180.png" />
  <link rel="icon" href="/icons/favicon-192x192.png" sizes="192x192" />
  <meta name="msapplication-TileImage" content="/icons/ms-favicon-270x270.png" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>{{ env('APP_NAME') }}</title>
  <link href="https://fonts.googleapis.com/css?family=Rubik:400,500,700&display=swap" rel="stylesheet">
  <link href="/css/fontawesome-pro-5.12.0-web/css/all.css" rel="stylesheet">
  <link href="/dist/app.css?v={{ env('STATIC_HASH') }}" rel="stylesheet">
  </link>
</head>

<body>
  <div id="app"></div>
  <script src="/dist/app.js?v={{ env('STATIC_HASH') }}"></script>
</body>

</html>