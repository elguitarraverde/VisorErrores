<?php

$raiz = dirname(__DIR__);

$finder = PhpCsFixer\Finder::create()
    ->in($raiz)
    ->exclude(['vendor', 'node_modules', 'cache', 'tmp', 'var', 'Releases', 'releases', 'dev'])
    ->name('*.php');

return (new PhpCsFixer\Config())
    ->setCacheFile(__DIR__ . '/.php-cs-fixer.cache')
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
        'no_unused_imports' => true,
        'ordered_imports' => ['sort_algorithm' => 'alpha'],
    ])
    ->setFinder($finder);
