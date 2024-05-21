<?php
/*
 * PHPCsFixer code style configuration file
 */
$config = new PhpCsFixer\Config();

return $config->
        setIndent("\t")->
        setRules([
                '@PhpCsFixer' => true,
                'blank_line_before_statement' => ['statements' => ['case', 'continue',
                        'declare', 'default', 'exit', 'goto', 'phpdoc', 'return', 'switch', 'throw', 'try', 'yield', ]],
                'single_space_around_construct' => true,
                'control_structure_braces' => true,
                'control_structure_continuation_position' => ['position' => 'next_line'],
                'declare_parentheses' => true,
                'no_multiple_statements_per_line' => true,
                'braces_position' => ['functions_opening_brace' => 'same_line', 'classes_opening_brace' => 'same_line'],
                'statement_indentation' => true,
                'no_extra_blank_lines' => true,
                'concat_space' => ['spacing' => 'one'],
                'indentation_type' => true,
                'operator_linebreak' => ['position' => 'end'],
                'ordered_class_elements' => false,
                'single_line_comment_style' => false,
                'single_quote' => false,
                'yoda_style' => ['equal' => false, 'identical' => false, 'less_and_greater' => false],
        ])->
        setFinder(
                PhpCsFixer\Finder::create()->
                        exclude(['vendor', 'node_modules', 'tests'])->
                        notPath('dev/autoloader.php')->
                        in(__DIR__)
        )
;
