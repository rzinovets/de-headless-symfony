<?php

namespace App\GraphQL\Resolver;

use App\Entity\Slider;
use App\Repository\SliderRepository;
use Symfony\Component\HttpFoundation\Request;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class SliderResolver implements QueryInterface, AliasedInterface
{
    /**
     * @var SliderRepository
     */
    private $sliderRepository;

    /**
     * @param SliderRepository $sliderRepository
     */
    public function __construct(
        SliderRepository $sliderRepository,
    ) {
        $this->sliderRepository = $sliderRepository;
    }

    /**
     * @param Argument $args
     * @return Slider|null
     */
    public function resolveBySliderKey(Argument $args): ?Slider {
        return $this->sliderRepository->findOneBy([
            'slider_key' => $args['slider_key']
        ]);
    }

    /**
     * @return array
     */
    public function resolveCollection(): array {
        return $this->sliderRepository->findAll();
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array {
        return [
            'resolveBySliderKey' => 'SliderByKey',
            'resolveCollection' => 'SliderCollection'
        ];
    }
}