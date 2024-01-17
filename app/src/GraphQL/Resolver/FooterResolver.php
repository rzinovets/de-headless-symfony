<?php

namespace App\GraphQL\Resolver;

use App\Repository\FooterRepository;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class FooterResolver implements QueryInterface, AliasedInterface
{
    /**
     * @var FooterRepository
     */
    private $footerRepository;

    /**
     * @param FooterRepository $footerRepository
     */
    public function __construct(
        FooterRepository $footerRepository
    ) {
        $this->footerRepository = $footerRepository;
    }

    /**
     * @return array
     */
    public function resolveCollection(): array {
        return $this->footerRepository->findAll();
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array {
        return [
            'resolveCollection' => 'FooterCollection',
        ];
    }
}