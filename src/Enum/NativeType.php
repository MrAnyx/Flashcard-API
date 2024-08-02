<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * @see https://www.php.net/manual/fr/function.gettype.php
 */
enum NativeType: string
{
    case BOOLEAN = 'boolean';
    case INTEGER = 'integer';

    /**
     * Pour des raisons historiques, "double" est retournée lorsqu'une valeur de type float est fournie, et non "float".
     */
    case DOUBLE = 'double';
    case STRING = 'string';
    case ARRAY = 'array';
    case OBJECT = 'object';
    case RESOURCE = 'resource';

    /**
     * À partir de PHP 7.2.0. Les ressources fermées sont maintenant rapportées en tant que 'resource (closed)'. Précédemment la valeur retournée pour des ressources fermées était 'unknown type'.
     */
    case RESOURCE_CLOSED = 'resource (closed)';
    case NULL = 'NULL';
    case UNKNOWN = 'unknown type';
}
