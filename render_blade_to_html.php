<?php
// file: render_blade_to_html.php

// Autoload Composer
require __DIR__ . '/vendor/autoload.php';

// Inisialisasi Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Fungsi untuk render Blade dan simpan ke file, dengan data
function renderBladeToFile($viewPath, $outputFile, $data = []) {
    $view = view($viewPath, $data);
    $html = $view->render();

    file_put_contents($outputFile, $html);
    echo "Render $viewPath -> $outputFile\n";
}

// Data yang mungkin diperlukan, jika tidak, bisa dihapus
$tokoCount = 0;
$userCount = 0;
$users = collect(); // kosong

// Hanya folder user yang ingin dirender
$viewsToRender = [
    'cart',
    'checkout',
    'detail-product',
    'home',
    'order',
    'welcome',
];

// Folder output di dalam folder Cordova
$outputFolder = __DIR__ . '/PacuApps/cordova/';

if (!is_dir($outputFolder)) {
    mkdir($outputFolder, 0755, true);
}

// Render semua view
foreach ($viewsToRender as $viewName) {
    $filename = str_replace('.', '-', $viewName) . '.html';
    $outputPath = $outputFolder . $filename;

    // Jika view membutuhkan data tertentu, bisa diatur disini
    $data = [];
    if ($viewName === 'home') {
        // Contoh data, jika dibutuhkan
        $data = [
            'userCount' => $userCount,
            'users' => $users,
        ];
    }

    renderBladeToFile($viewName, $outputPath, $data);
}

echo "Selesai render semua view.\n";
?>
