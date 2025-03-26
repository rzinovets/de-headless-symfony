<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;

class FAQType extends ObjectType
{
    public function __construct()
    {
        parent::__construct([
            'name' => 'FAQ',
            'fields' => [
                'id' => Type::int(),
                'question' => Type::string(),
                'answer' => Type::string(),
                'priority' => Type::int(),
            ],
        ]);
    }
}