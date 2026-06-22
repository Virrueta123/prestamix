<?php
header('Content-Type: text/plain; charset=utf-8');

echo "=== PRUEBA HTACCESS vs ARCHIVOS ===\n\n";
echo ".htaccess NO afecta scandir() ni file_exists().\n";
echo "Si aqui solo aparece 'public', los archivos NO estan en esta ruta.\n\n";

$parent = dirname(__DIR__);
echo "Ruta padre: $parent\n\n";
echo "Carpetas/archivos que PHP VE:\n";
foreach (array_diff(scandir($parent), ['.', '..']) as $item) {
    echo "  - $item\n";
}

echo "\nvendor/autoload.php: " . (file_exists($parent . '/vendor/autoload.php') ? 'SI' : 'NO') . "\n";
echo "public/.htaccess:    " . (file_exists(__DIR__ . '/.htaccess') ? 'SI' : 'NO') . "\n";
echo "raiz/.htaccess:      " . (file_exists($parent . '/.htaccess') ? 'SI' : 'NO') . "\n";