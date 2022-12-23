<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude('vendor')
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR2'                                 => true,
        'array_syntax'                          => ['syntax' => 'short'],
        'concat_space'                          => ['spacing' => 'one'],
        'ordered_imports'                       => true,
        'single_blank_line_at_eof'              => true,
        'random_api_migration'                  => true,
        'no_unused_imports'                     => true,
        'phpdoc_var_without_name'               => true,
        'return_type_declaration'               => true,
        'trailing_comma_in_multiline'           => true,
        'no_whitespace_in_blank_line'           => true,
        'single_quote'                          => true,
        'new_with_braces'                       => true,
        'blank_line_before_statement'           => true,
        'phpdoc_align'                          => ['align' => 'vertical'],
        'phpdoc_separation'                     => true,
        'phpdoc_summary'                        => false,
        'linebreak_after_opening_tag'           => true,
        'mb_str_functions'                      => true,
        'no_php4_constructor'                   => true,
        'no_unreachable_default_argument_value' => true,
        'no_useless_else'                       => true,
        'no_useless_return'                     => true,
        'php_unit_strict'                       => true,
        'phpdoc_order'                          => true,
        'strict_comparison'                     => true,
        'strict_param'                          => true,
        'binary_operator_spaces'                => [
            'operators' => [
                '='  => 'align',
                '=>' => 'align',
            ],
        ],
        'native_function_invocation'            => [
            'include' => ['@internal'],
        ],
        'blank_line_between_import_groups'      => false,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ;
