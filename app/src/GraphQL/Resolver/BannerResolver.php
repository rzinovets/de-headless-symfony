<?php

namespace App\GraphQL\Resolver;

use App\Repository\BannerRepository;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

readonly class BannerResolver implements QueryInterface, AliasedInterface
{
    /**
     * @param BannerRepository $bannerRepository
     */
    public function __construct(
        private BannerRepository $bannerRepository
    ) {
    }

    /**
     * @return array
     */
    public function resolveCollection(): array {
        return $this->bannerRepository->findAll();
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array {
        return [
            'resolveCollection' => 'BannerCollection',
        ];
    }
}