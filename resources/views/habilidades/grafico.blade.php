<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gráfico de Barras</title>
    {!! Charts::assets() !!} <!-- Carga los assets necesarios para los gráficos -->
</head>
<body>
    <h1>Gráfico de Barras</h1>
    <div>
        {!! $chart->container() !!} <!-- Renderiza el contenedor del gráfico -->
    </div>

    <script>
        {!! $chart->script() !!} <!-- Carga el script del gráfico -->
    </script>
</body>
</html>
