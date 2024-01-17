<?php

namespace App\GraphQL\Resolver;

use App\Entity\Block;
use App\Repository\BlockRepository;
use Overblog\GraphQLBundle\Definition\Argument;
use Overblog\GraphQLBundle\Definition\Resolver\AliasedInterface;
use Overblog\GraphQLBundle\Definition\Resolver\QueryInterface;
use Symfony\Component\HttpFoundation\Request;

class BlockResolver implements QueryInterface, AliasedInterface
{
    /**
     * @var BlockRepository
     */
    private $blockRepository;

    /**
     * @param BlockRepository $blockRepository
     */
    public function __construct(
        BlockRepository $blockRepository
    ) {
        $this->blockRepository = $blockRepository;
    }

    /**
     * @param Argument $args
     * @return Block|null
     */
    public function resolveByIdentifier(Argument $args): ?Block {
        $block = $this->blockRepository->findOneBy(
            [
                'identifier' => $args['identifier']
            ]
        );

        $request = Request::createFromGlobals();

        $imageUrl = $request->getUriForPath('/media');

        $block->setMediaUrl($imageUrl);

        return $block;
    }

    /**
     * @return string[]
     */
    public static function getAliases(): array {
        return [
            'resolveByIdentifier' => 'BlockByIdentifier',
        ];
    }
}