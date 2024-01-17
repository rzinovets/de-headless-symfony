<?php

namespace App\GraphQL\Resolver;

use App\Entity\ConfigValues;
use App\Repository\ConfigValuesRepository;
use App\Repository\ConfigGroupsRepository;
use App\Repository\ConfigLabelsRepository;
use Doctrine\DBAL\Exception;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;

class ConfigResolver implements QueryInterface, AliasedInterface
{
    /**
     * @var ConfigValuesRepository
     */
    private $configValuesRepository;

    /**
     * @var ConfigGroupsRepository
     */
    private $configGroupsRepository;

    /**
     * @var ConfigLabelsRepository
     */
    private $configLabelsRepository;

    /**
     * @param ConfigValuesRepository $configValuesRepository
     * @param ConfigGroupsRepository $configGroupsRepository
     * @param ConfigLabelsRepository $configLabelsRepository
     */
    public function __construct(
        ConfigValuesRepository $configValuesRepository,
        ConfigGroupsRepository $configGroupsRepository,
        ConfigLabelsRepository $configLabelsRepository
    ) {
        $this->configValuesRepository = $configValuesRepository;
        $this->configGroupsRepository = $configGroupsRepository;
        $this->configLabelsRepository = $configLabelsRepository;
    }

    /**
     * @throws Exception
     */
    public function resolve(Argument $args): ?ConfigValues {
        $splitArgs = explode("/", $args['code']);

        $configGroup = $this->configGroupsRepository->findOneBy(
            ['code' => $splitArgs[0]]
        );

        $configLabel = $this->configLabelsRepository->findOneBy(
            [
                'group_id' => $configGroup->getId(),
                'code' => $splitArgs[1],
                'is_secure' => '0'
            ]
        );

        if ($configLabel) {
            return $this->configValuesRepository->find($args['code']);
        }
        throw new Exception('Is secure');
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array {
        return [
            'resolve' => 'Config'
        ];
    }
}
