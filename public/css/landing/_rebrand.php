<?php
/**
 * Remap Sana purple kit → TADRIS LAB blue/yellow (config/academy-theme.php).
 * php public/css/landing/_rebrand.php
 */
declare(strict_types=1);

$dir = __DIR__;
$files = glob($dir . '/*.css') ?: [];

$map = [
    // CSS variables first
    '#6D28D9' => '#0B3D91',
    '#6d28d9' => '#0B3D91',
    '#5B21B6' => '#072A66',
    '#5b21b6' => '#072A66',
    '#4C1D95' => '#051F4D',
    '#4c1d95' => '#051F4D',
    '#8B5CF6' => '#3D6BC4',
    '#8b5cf6' => '#3D6BC4',
    '#A78BFA' => '#6B8FD4',
    '#a78bfa' => '#6B8FD4',
    '#7C3AED' => '#1A56B0',
    '#7c3aed' => '#1A56B0',
    '#FBBF24' => '#F5B800',
    '#fbbf24' => '#F5B800',
    '#F59E0B' => '#D99E00',
    '#f59e0b' => '#D99E00',
    '#FCD34D' => '#FFD24D',
    '#fcd34d' => '#FFD24D',
    '#FDE68A' => '#FFE9A8',
    '#fde68a' => '#FFE9A8',
    '#F8F7FC' => '#F4F7FC',
    '#f8f7fc' => '#F4F7FC',
    '#1e1b4b' => '#0B1220',
    '#EDE9FE' => '#E8EEF8',
    '#ede9fe' => '#E8EEF8',
    '#F5F3FF' => '#EEF3FB',
    '#f5f3ff' => '#EEF3FB',
    '#DDD6FE' => '#C5D4F0',
    '#ddd6fe' => '#C5D4F0',
    // rgba purple families
    'rgba(91,33,182,' => 'rgba(11,61,145,',
    'rgba(91, 33, 182,' => 'rgba(11, 61, 145,',
    'rgba(109,40,217,' => 'rgba(11,61,145,',
    'rgba(109, 40, 217,' => 'rgba(11, 61, 145,',
    'rgba(76,29,149,' => 'rgba(5,31,77,',
    'rgba(167,139,250,' => 'rgba(107,143,212,',
    'rgba(251,191,36,' => 'rgba(245,184,0,',
    'rgba(245,184,0,' => 'rgba(245,184,0,', // already yellow
    // auth geo extras keep blue primary already close; nudge purple brand refs
    '#6A2CFF' => '#0B3D91',
    '#5520CC' => '#072A66',
    '#F0EBFF' => '#E8EEF8',
    '#1D4EDB' => '#0B3D91',
    '#1639B0' => '#072A66',
    '#F4B000' => '#F5B800',
];

foreach ($files as $file) {
    if (str_ends_with($file, '_rebrand.php')) {
        continue;
    }
    $css = (string) file_get_contents($file);
    $out = strtr($css, $map);
    // Root token aliases for clarity
    $out = preg_replace(
        '/:root\s*\{/',
        ":root {\n    /* TADRIS LAB brand: blue #0B3D91 + yellow #F5B800 (from designs/public-pages structure) */",
        $out,
        1
    ) ?? $out;
    file_put_contents($file, $out);
    echo basename($file) . " OK\n";
}

echo "Done\n";
