<?php

namespace App\GraphQL\Resolver;

use App\Model\MenuTree;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class MenuResolver extends MenuTree implements QueryInterface, AliasedInterface
{
    /**
     * @return array
     */
    public function resolveCollection(): array
    {
        return parent::createArrayOfMenuTree();
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array
    {
        return [
            'resolveCollection' => 'Menu'
        ];
    }
}