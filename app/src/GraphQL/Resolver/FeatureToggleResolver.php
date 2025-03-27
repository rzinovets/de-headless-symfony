<?php

namespace App\GraphQL\Resolver;

use App\Entity\FeatureToggle;
use App\Repository\FeatureToggleRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

readonly class FeatureToggleResolver implements QueryInterface, AliasedInterface
{
    /**
     * @param FeatureToggleRepository $featureToggleRepository
     */
    public function __construct(private FeatureToggleRepository $featureToggleRepository)
    {
    }

    /**
     * @param Argument $args
     * @return FeatureToggle|null
     */
    public function resolve(Argument $args): ?FeatureToggle
    {
        return $this->featureToggleRepository->findOneBy(['code' => $args['code']]);
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array
    {
        return [
            'resolve' => 'FeatureToggle',
        ];
    }
}