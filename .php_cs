<?php
use PhpCsFixer\Config;
use PhpCsFixer\Finder;
return Config::create()
    ->setUsingCache(true)
    ->setFinder(
        Finder::create()
            ->exclude([
                'vendor',
            ])
            ->in(__DIR__)
    )
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
        'class_definition' => [
            'multiLineExtendsEachSingleLine' => true,
        ],
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'align_double_arrow' => false,
            'align_equals' => false,
        ],
        'concat_space' => [
            'spacing' => 'one',
        ],
        'general_phpdoc_annotation_remove' => [
            'author',
            'package',
        ],
        'no_multiline_whitespace_before_semicolons' => true,
        'no_null_property_initialization' => true,
        'no_useless_else' => true,
        'no_useless_return' => true,
        'ordered_class_elements' => [
            'order' => [
                'use_trait',
                'constant_public',
                'constant_protected',
                'constant_private',
                'property_public',
                'property_protected',
                'property_private',
                'construct'
            ]
        ],
        'ordered_imports' => [
            'sortAlgorithm' => 'alpha'
        ],
        'phpdoc_order' => true,
        'phpdoc_types_order' => [
            'null_adjustment' => 'always_last'
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_annotation_without_dot' => true,
        'yoda_style' => false
    ]);