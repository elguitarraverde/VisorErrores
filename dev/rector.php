<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

$raiz = dirname(__DIR__);

return RectorConfig::configure()
    ->withPaths([$raiz])
    ->withSkip([
        $raiz . '/vendor',
        $raiz . '/node_modules',
        $raiz . '/cache',
        $raiz . '/tmp',
        $raiz . '/var',
        $raiz . '/Releases',
        $raiz . '/releases',
        $raiz . '/dev',
    ])
    ->withBootstrapFiles([
        dirname($raiz, 2) . '/vendor/autoload.php',
        $raiz . '/vendor/autoload.php',
    ])
    ->withPhpSets(php84: true);
