<?php

$finder = PhpCsFixer\Finder::create();

$finder->in([
    __DIR__.'/packages/*/src',
    __DIR__.'/packages/*/tests',
    'tests',
]);

$config = new PhpCsFixer\Config();
$config
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PhpCsFixer' => true,
        'phpdoc_align' => false,
        'phpdoc_annotation_without_dot' => false,
        'single_import_per_statement' => false,
        'global_namespace_import' => false,
    ])
;

return $config;
