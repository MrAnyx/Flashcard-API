<?php

declare(strict_types=1);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setFinder(PhpCsFixer\Finder::create()
        ->in(__DIR__)
        ->exclude('var')
        ->exclude('vendor'))
    ->setRules([
        '@Symfony:risky' => true,
        '@Symfony' => true,
        // Each element of an array must be indented exactly once.
        'array_indentation' => true,
        // PHP attributes declared without arguments must (not) be followed by empty parentheses.
        'attribute_empty_parentheses' => true,
        // Converts FQCN strings to `*::class` keywords.
        'class_keyword' => true,
        // Using `isset($var) &&` multiple times should be done in one call.
        'combine_consecutive_issets' => true,
        // Calling `unset` on multiple items should be done in one call.
        'combine_consecutive_unsets' => true,
        // Concatenation should be spaced according to configuration.
        'concat_space' => ['spacing' => 'one'],
        // Class `DateTimeImmutable` should be used instead of `DateTime`.
        'date_time_immutable' => true,
        // Force strict types declaration in all files. Requires PHP >= 7.0.
        'declare_strict_types' => true,
        // Escape implicit backslashes in strings and heredocs to ease the understanding of which are special chars interpreted by PHP and which not.
        'escape_implicit_backslashes' => true,
        // Converts implicit variables into explicit ones in double-quoted strings or heredoc syntax.
        'explicit_string_variable' => true,
        // List (`array` destructuring) assignment should be declared using the configured syntax. Requires PHP >= 7.1.
        'list_syntax' => true,
        // Replace non multibyte-safe functions with corresponding mb function.
        'mb_str_functions' => true,
        // Method chaining MUST be properly indented. Method chaining with different levels of indentation is not supported.
        'method_chaining_indentation' => true,
        // Forbid multi-line whitespace before the closing semicolon or move the semicolon to the new line for chained calls.
        'multiline_whitespace_before_semicolons' => true,
        // There should not be useless `else` cases.
        'no_useless_else' => true,
        // There should not be an empty `return` statement at the end of a function.
        'no_useless_return' => true,
        // Orders the interfaces in an `implements` or `interface extends` clause.
        // All items of the given PHPDoc tags must be either left-aligned or (by default) aligned vertically.
        'phpdoc_align' => ['align' => 'left'],
        // Inside an enum or `final`/anonymous class, `self` should be preferred over `static`.
        'self_static_accessor' => true,
        // A return statement wishing to return `void` should not return `null`.
        'simplified_null_return' => true,
        // Handles implicit backslashes in strings and heredocs. Depending on the chosen strategy, it can escape implicit backslashes to ease the understanding of which are special chars interpreted by PHP and which not (`escape`), or it can remove these additional backslashes if you find them superfluous (`unescape`). You can also leave them as-is using `ignore` strategy.
        'string_implicit_backslashes' => ['single_quoted' => 'escape'],
        // Use `null` coalescing operator `??` where possible. Requires PHP >= 7.0.
        'ternary_to_null_coalescing' => true,
        // Add `void` return type to functions with missing or empty return statements, but priority is given to `@return` annotations. Requires PHP >= 7.1.
        'void_return' => true,
        // Write conditions in Yoda style (true), non-Yoda style (['equal' => false, 'identical' => false, 'less_and_greater' => false]) or ignore those conditions (null) based on configuration.
        'yoda_style' => false,
    ]);
