<!DOCTYPE html>
<html lang="es-PE">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bienvenido a Nexus</title>
  @include('panels.styles')
  @yield('styles')
</head>
<body>
  @yield('content')
  @include('panels.scripts')
  @yield('scripts')
</body>
</html>