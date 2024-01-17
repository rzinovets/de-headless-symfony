<?php

namespace App\GraphQL\Type;

use DateTime;
use Exception;
use GraphQL\Language\AST\Node;

class DateTimeType
{
    public static function serialize(DateTime $value): string
    {
        return $value->format('c');
    }

    /**
     * @throws Exception
     */
    public static function parseValue(string $value): Datetime
    {
        return new DateTime($value);
    }

    /**
     * @throws Exception
     */
    public static function parseLiteral(Node $valueNode): DateTime
    {
        return new DateTime($valueNode->value);
    }
}
