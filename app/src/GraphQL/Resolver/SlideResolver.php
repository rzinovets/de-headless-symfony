<?php

namespace App\GraphQL\Resolver;

use App\Repository\SlideRepository;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class SlideResolver implements QueryInterface, AliasedInterface
{
    /**
     * @var SlideRepository
     */
    private $slideRepository;

    /**
     * @param SlideRepository $slideRepository
     */
    public function __construct(
        SlideRepository $slideRepository
    ) {
        $this->slideRepository = $slideRepository;
    }

    /**
     * @return array
     */
    public function resolveCollection(): array {
        return $this->slideRepository->findAll();
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array {
        return [
            'resolveCollection' => 'SlideCollection',
        ];
    }
}