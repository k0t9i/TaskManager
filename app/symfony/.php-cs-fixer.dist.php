<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        //dirname(__DIR__).'/src/',
        __DIR__.'/tests',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
