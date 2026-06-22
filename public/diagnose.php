<?php
header('Content-Type: text/html; charset=utf-8');
echo '<h2>Diagnostico - 1 sola prueba</h2><pre>';

$base = dirname(__DIR__);

echo "PHP corre desde:  " . __DIR__ . "\n";
echo "Carpeta padre:    " . $base . "\n\n";

echo "=== LO QUE PHP VE EN LA CARPETA PADRE ===\n";
foreach (array_diff(scandir($base), ['.', '..']) as $item) {
    echo '  - ' . $item . "\n";
}

echo "\n=== ARCHIVOS CLAVE ===\n";
$files = ['vendor/autoload.php', 'app', 'bootstrap/app.php', '.env', 'artisan'];
foreach ($files as $f) {
    $path = $base . '/' . $f;
    echo str_pad($f . ':', 22) . (file_exists($path) ? 'SI' : 'NO') . "\n";
}

echo "\n=== PRUEBA DEFINITIVA ===\n";
$marker = $base . '/PRUEBA_HOSTING.txt';
if (file_exists($marker)) {
    echo "ENCONTRADO: PRUEBA_HOSTING.txt en la carpeta padre.\n";
    echo "Contenido: " . file_get_contents($marker) . "\n";
    echo "=> PHP y el administrador miran LA MISMA carpeta.\n";
} else {
    echo "NO EXISTE: PRUEBA_HOSTING.txt en la carpeta padre.\n";
    echo "=> Debes crear ese archivo en el administrador, al lado de vendor/public,\n";
    echo "   NO dentro de public. Si PHP sigue sin verlo, el dominio apunta a otra ruta.\n";
}

echo '</pre>';