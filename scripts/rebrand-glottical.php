<?php

/**
 * Replace legacy Muallimx branding in source files (not vendor/storage).
 * Usage: php scripts/rebrand-tadrislab.php
 */

$root = dirname(__DIR__);
$extensions = ['php', 'blade.php', 'js', 'json', 'md', 'sql'];
$skipDirs = ['vendor', 'node_modules', 'storage', '.git'];
$skipFiles = [
    'scripts/rebrand-tadrislab.php',
    'app/Services/MuallimxAiClient.php',
    'config/muallimx_ai.php',
];

$replacements = [
    "config('app.name', 'Muallimx')" => "config('app.name')",
    'config("app.name", "Muallimx")' => 'config("app.name")',
    "config('app.name', 'TADRIS LAB')" => "config('app.name')",
    'config("app.name", "TADRIS LAB")' => 'config("app.name")',
    'Muallimx Academy' => 'TADRIS LAB',
    'Muallimx Classroom' => 'TADRIS LAB Classroom',
    'Muallimx AI' => 'TADRIS AI',
    'Muallimx —' => 'TADRIS LAB —',
    'منصة Muallimx' => 'منصة تدريس لاب',
    'منصة Muallimx' => 'منصة تدريس لاب',
    'معرض Muallimx' => 'معرض TADRIS LAB',
    'فريق Muallimx' => 'فريق TADRIS LAB',
    'Muallimx administration' => 'TADRIS LAB administration',
    'إدارة Muallimx' => 'إدارة TADRIS LAB',
    'Muallimx' => 'TADRIS LAB',
    "'Muallimx'" => "'TADRIS LAB'",
    '"Muallimx"' => '"TADRIS LAB"',
    'muallimx-shell-v' => 'tadrislab-shell-v',
    'muallimx-board-' => 'tadrislab-board-',
    'Muallimx-' => 'TADRIS LAB-',
];

$changed = 0;
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
    if (! $file->isFile()) {
        continue;
    }

    $relative = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    foreach ($skipDirs as $skip) {
        if (str_starts_with($relative, $skip.'/') || $relative === $skip) {
            continue 2;
        }
    }
    if (in_array($relative, $skipFiles, true)) {
        continue;
    }

    $ext = $file->getExtension();
    if ($ext === 'php' && str_ends_with($relative, '.blade.php')) {
        $ext = 'blade.php';
    }
    if (! in_array($ext, $extensions, true) && ! str_ends_with($relative, '.blade.php')) {
        continue;
    }

    $content = file_get_contents($file->getPathname());
    $original = $content;
    foreach ($replacements as $from => $to) {
        $content = str_replace($from, $to, $content);
    }

    if ($content !== $original) {
        file_put_contents($file->getPathname(), $content);
        $changed++;
        echo "updated: $relative\n";
    }
}

echo "Done. $changed files updated.\n";
