<?php

namespace App\GraphQL\Resolver;

use FOS\ElasticaBundle\Finder\PaginatedFinderInterface;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

readonly class SearchResolver implements QueryInterface, AliasedInterface
{
    /**
     * @param PaginatedFinderInterface $faqFinder
     */
    public function __construct(private PaginatedFinderInterface $faqFinder)
    {
    }

    /**
     * @param Argument $args
     * @return array
     */
    public function resolve(Argument $args): array
    {
        $query = $args['query'];
        $limit = $args['limit'] ?? 10;

        $results = $this->faqFinder->findHybrid($query, $limit);

        return array_map(function($item) {
            return [
                'id' => $item->getTransformed()->getId(),
                'question' => $item->getTransformed()->getQuestion(),
                'answer' => $item->getTransformed()->getAnswer(),
                'priority' => $item->getTransformed()->getPriority(),
            ];
        }, $results);
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array
    {
        return [
            'resolve' => 'FAQSearch',
        ];
    }
}