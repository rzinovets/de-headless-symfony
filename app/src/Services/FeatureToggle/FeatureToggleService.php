<?php

namespace App\Services\FeatureToggle;

use App\Entity\FeatureToggle;
use App\Repository\FeatureToggleRepository;

readonly class FeatureToggleService
{
    /**
     * @param FeatureToggleRepository $featureToggleRepository
     */
    public function __construct(
        private FeatureToggleRepository $featureToggleRepository
    ) {
    }

    /**
     * @param string $code
     * @return bool
     */
    public function enabled(string $code): bool
    {
        $featureToggle = $this->featureToggleRepository->findOneBy(['code' => $code]);

        if (!$featureToggle instanceof FeatureToggle) {
            return false;
        }

        return $featureToggle->isEnabled();
    }
}