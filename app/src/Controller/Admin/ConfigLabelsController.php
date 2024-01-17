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
    const CONFIG_FORM = 'admin/configForm.html.twig';
    const SAVE_URL = '/admin/config/save';

    /**
     * @var ManagerRegistry
     */
    private ManagerRegistry $em;
    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $groupRepository;
    /**
     * @var ObjectRepository
     */
    protected ObjectRepository $valueRepository;
    /**
     * @var AdminUrlGenerator
     */
    protected AdminUrlGenerator $adminUrlGenerator;

    /**
     * @param ManagerRegistry $registry
     * @param AdminUrlGenerator $adminUrlGenerator
     */
    public function __construct(
        ManagerRegistry   $registry,
        AdminUrlGenerator $adminUrlGenerator
    ) {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->em = $registry;
        $this->groupRepository = $this->em->getRepository(ConfigGroups::class);
        $this->valueRepository = $this->em->getRepository(ConfigValues::class);
    }

    /**
     * @return Response
     */
    #[Route('/admin/config', name: 'admin_config')]
    public function index(): Response {
        $configArray = $this->formAnArray();

        return $this->render(self::CONFIG_FORM, [
            'config' => $configArray,
            'url' => self::SAVE_URL
        ]);
    }

    /**
     * @param Request $request
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route(self::SAVE_URL, name: 'config_save')]
    public function save(Request $request, ManagerRegistry $doctrine): Response {
        $entityManager = $doctrine->getManager();

        $configParams = $request->request->all();

        foreach ($configParams as $configCode => $configValue) {
            $configValues = $this->valueRepository->find($configCode);
            if ($configValues === null) {
                $configValues = new ConfigValues();
            }

            $configValues->setValue($configValue);
            $configValues->setCode($configCode);
            $entityManager->persist($configValues);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_config');
    }

    /**
     * @return array
     */
    public function formAnArray(): array {
        $groupCollection = $this->groupRepository->findAll();

        $configArray = [];

        foreach ($groupCollection as $configGroupEntity) {
            $configArray[$configGroupEntity->getCode()] = [
                'label_data' => [],
                'code' => $configGroupEntity->getCode(),
                'label' => $configGroupEntity->getLabel()
            ];
            $labelData = $this->createArrayLabelData($configGroupEntity);
            $configArray[$configGroupEntity->getCode()]['label_data'] = $labelData;
        }
        return $configArray;
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
                'value' => $configArrayOfValues[$codeValue] ?? ''
            ];
        }
        return $labelData;
    }
}
