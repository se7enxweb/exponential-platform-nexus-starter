<?php

// To support running PHP CS Fixer via PHAR file (e.g. in GitHub Actions)
require_once __DIR__ . '/vendor/netgen/coding-standard/lib/PhpCsFixer/Config.php';

return (new Netgen\CodingStandard\PhpCsFixer\Config())
    ->addRules([
        'phpdoc_no_alias_tag' => false,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in('bundle')
    )
;
