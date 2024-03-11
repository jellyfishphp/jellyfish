<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/packages/*/src/',
        __DIR__ . '/packages/*/tests/',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
    ])->setFinder($finder);
