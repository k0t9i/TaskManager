<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        dirname(__DIR__).'/src/',
        dirname(__DIR__).'/tests/',
    ]);

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
    ])
    ->setFinder($finder)
;
