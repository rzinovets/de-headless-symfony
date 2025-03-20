<?php

namespace App\Controller\Admin;

use App\Entity\ConfigGroups;
use App\Entity\ConfigValues;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConfigLabelsController extends DashboardController
{
    private const CONFIG_FORM = 'admin/configForm.html.twig';
    private const SAVE_URL = '/admin/config/save';

    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $groupRepository;

    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $valueRepository;

    /**
     * @param ManagerRegistry $registry
     * @param AdminUrlGenerator $adminUrlGenerator
     */
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly AdminUrlGenerator $adminUrlGenerator,
    ) {
        $this->groupRepository = $this->registry->getRepository(ConfigGroups::class);
        $this->valueRepository = $this->registry->getRepository(ConfigValues::class);
    }

    /**
     * @return Response
     */
    #[Route('/admin/config', name: 'admin_config')]
    public function index(): Response
    {
        $configArray = $this->formAnArray();

        return $this->render(self::CONFIG_FORM, [
            'config' => $configArray,
            'url' => self::SAVE_URL,
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(self::SAVE_URL, name: 'config_save')]
    public function save(Request $request): Response
    {
        $entityManager = $this->registry->getManager();
        $configParams = $request->request->all();

        foreach ($configParams as $configCode => $configValue) {
            $configValues = $this->valueRepository->find($configCode) ?? new ConfigValues();
            $configValues->setValue($configValue);
            $configValues->setCode($configCode);
            $entityManager->persist($configValues);
        }

        $entityManager->flush();

        return $this->redirectToRoute('admin_config');
    }

    /**
     * @return array
     */
    private function formAnArray(): array
    {
        return array_map(fn($configGroupEntity) => [
            'label_data' => $this->createArrayLabelData($configGroupEntity),
            'code' => $configGroupEntity->getCode(),
            'label' => $configGroupEntity->getLabel(),
        ], $this->groupRepository->findAll());
    }

    /**
     * @param $configLabelsEntity
     * @return array
     */
    public function createArrayOfOptions($configLabelsEntity): array {
        $optionData = [];

        foreach ($configLabelsEntity->getOptions() as $configOptionsEntity) {
            $optionId = $configOptionsEntity->getId();
            $optionData[$optionId] = $configOptionsEntity->getText();
        }
        return $optionData;
    }

    /**
     * @param $configGroupEntity
     * @return array
     */
    public function createArrayLabelData($configGroupEntity): array {
        $valueCollection = $this->valueRepository->findAll();

        $labelData = [];
        $configArrayOfValues = [];

        foreach ($valueCollection as $value) {
            $configArrayOfValues[$value->getCode()] = $value->getValue();
        }

        foreach ($configGroupEntity->getLabels() as $configLabelsEntity) {
            $codeValue = $configGroupEntity->getCode() . '/' . $configLabelsEntity->getCode();
            $optionData = $this->createArrayOfOptions($configLabelsEntity);
            $labelCode = $configLabelsEntity->getCode();
            $labelData[$labelCode] = [
                'option_data' => $optionData,
                'label' => $configLabelsEntity->getLabel(),
                'code' => $configLabelsEntity->getCode(),
                'type' => $configLabelsEntity->getType(),
                'value' => $configArrayOfValues[$configLabelsEntity->getCode()] ?? ''
            ];
        }
        return $labelData;
    }
}
