<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('var')
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony:risky' => true,
        'concat_space' => [
            'spacing' => 'one',
        ],
        'ordered_imports' => [
            'sort_algorithm' => 'length',
            'imports_order' => [
                'const',
                'class',
                'function',
            ],
        ],
        'single_trait_insert_per_statement' => true,
        'fully_qualified_strict_types' => true,
        'phpdoc_align' => [
            'align' => 'left',
        ],
        "final_class" => true,
        'ordered_interfaces' => [
            'order' => 'alpha',
            'direction' => 'ascend',
        ],
        'yoda_style' => false,
    ])
    ->setFinder($finder)
;
