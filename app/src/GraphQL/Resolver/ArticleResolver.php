<?php

namespace App\GraphQL\Resolver;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class ArticleResolver implements QueryInterface, AliasedInterface
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @param ArticleRepository $articleRepository
     */
    public function __construct(
        ArticleRepository $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @param Argument $args
     * @return Article|null
     */
    public function resolve(Argument $args): ?Article {
        return $this->articleRepository->find($args['id']);
    }

    /**
     * @param Argument $args
     * @return Article|null
     */
    public function resolveByUrlKey(Argument $args): ?Article {
        return $this->articleRepository->findOneBy(
            [
                'url_key' => $args['url_key']
            ]
        );
    }

    /**
     * @param Argument $args
     * @return array
     */
    public function resolveCollection(Argument $args): array {
        return $this->articleRepository->findBy(array(), array('id' => 'DESC'), $args['limit'], 0);
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array {
        return [
            'resolveCollection' => 'ArticleCollection',
            'resolveByUrlKey' => 'ArticleByKey',
            'resolve' => 'Article'
        ];
    }
}