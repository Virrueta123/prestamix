<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Prestamo pendiente</title>
  <link rel="icon" href="https://w7.pngwing.com/pngs/793/681/png-transparent-the-coca-cola-company-fizzy-drinks-beverages-mangolia-food-text-logo.png" type="image/png">
  <!-- Enlace al archivo CSS de Bootstrap -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
  <div class="card" style="width: 18rem;">
    
    <div class="card-body">
      <h2>{{ $saludo }} {{$nombre}}</h2>
      <h5 class="card-title">
       El cliente {{$cliente}} ha solicitado en prestamo
      </h5> 
      <a href="{{$ruta}}" class="btn btn-primary">Mira la solicitud del prestamo</a>
    </div>
  </div>
</div>

<!-- Enlace al archivo JS de Bootstrap (requerido para algunos componentes de Bootstrap) -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>