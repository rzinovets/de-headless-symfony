<?php

namespace App\Command;

use App\Entity\TelegramGroup;
use App\Repository\TelegramGroupRepository;
use App\Services\Notification\Notifications\TelegramNotification;
use App\Services\Notification\NotificationService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\File;
use Exception;
use RuntimeException;

class GeneratePriceCommand extends Command
{
    protected static $defaultName = 'app:generate-price';

    /**
     * @var NotificationService
     */
    private NotificationService $notificationService;

    /**
     * @var TelegramGroupRepository
     */
    private TelegramGroupRepository $telegramGroupRepository;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param NotificationService $notificationService
     * @param TelegramGroupRepository $telegramGroupRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        NotificationService $notificationService,
        TelegramGroupRepository $telegramGroupRepository,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->notificationService = $notificationService;
        $this->telegramGroupRepository = $telegramGroupRepository;
        $this->logger = $logger;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Generates price list from Google Sheets and sends to Telegram')
            ->setHelp('This command downloads price list, converts prices to BYN and sends to Telegram group');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->handleExcel();
            $output->writeln('Price list successfully processed and sent');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->logger->error('Error in GeneratePriceCommand: ' . $e->getMessage());
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function handleExcel(): void
    {
        ini_set('memory_limit', '512M');

        try {
            $exchangeRate = $this->getExchangeRate();
            $markupPercent = 30;

            $spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/1clohCQ_K13d9PZDjhTdZgkO2n_jw05jpTW5W8o-LZhA/export?format=xlsx';
            $fileContent = file_get_contents($spreadsheetUrl);

            if (!$fileContent) {
                throw new RuntimeException('Не удалось загрузить файл из Google Sheets');
            }

            $tempFilePath = tempnam(sys_get_temp_dir(), 'price_') . '.xlsx';
            file_put_contents($tempFilePath, $fileContent);

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $reader->setReadEmptyCells(false);

            $spreadsheet = $reader->load($tempFilePath);
            $sheet = $spreadsheet->getActiveSheet();

            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);

                foreach ($cellIterator as $cell) {
                    $value = $cell->getValue();

                    $newValue = $this->convertPriceFormats($value, $exchangeRate, $markupPercent);

                    if ($newValue !== null) {
                        $sheet->setCellValue($cell->getCoordinate(), $newValue);
                    }

                    unset($value, $cell);
                }
                unset($row, $cellIterator);
                gc_collect_cycles();
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save($tempFilePath);

            $text = $this->notificationService->generateTelegramText(
                'notification/telegram/current-shishka-exel.twig',
                []
            );

            $group = $this->telegramGroupRepository->findByCode(TelegramGroup::CODE_GROUP_NOTIFY_CONTACT_FORM);

            $tgNotification = (new TelegramNotification())
                ->setGroup($group)
                ->setText($text)
                ->setAttachment(new File($tempFilePath));

            $this->notificationService->send([$tgNotification]);

        } catch (Exception $e) {
            $this->logger->error('Ошибка при отправке прайса в Telegram: ' . $e->getMessage());
            throw $e;
        } finally {
            if (isset($tempFilePath) && file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
            ini_restore('memory_limit');
        }
    }

    /**
     * @param string|null $value
     * @param float $exchangeRate
     * @param float $markupPercent
     * @return string|null
     */
    private function convertPriceFormats(?string $value, float $exchangeRate, float $markupPercent): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (preg_match('/(\d+)\s?Р/u', $value, $matches)) {
            $priceRub = (float)$matches[1];
            $priceByn = ceil($priceRub * $exchangeRate / 100 * (1 + $markupPercent / 100));
            return $priceByn . ' BYN';
        }

        if (preg_match('/ОПТ ОТ (\d+) ТЫСЯЧ/ui', $value, $matches)) {
            $priceRub = (float)$matches[1] * 1000;
            $priceByn = ceil($priceRub * $exchangeRate / 100 * (1 + $markupPercent / 100));
            return 'ОПТ ОТ ' . $priceByn . ' BYN';
        }

        if (preg_match('/(От \d+ штук-)(\d+)( рублей)/ui', $value, $matches)) {
            $priceRub = (float)$matches[2];
            $priceByn = ceil($priceRub * $exchangeRate / 100 * (1 + $markupPercent / 100));
            return $matches[1] . $priceByn . $matches[3];
        }

        if (preg_match('/(\d+)( рублей)/ui', $value, $matches)) {
            $priceRub = (float)$matches[1];
            $priceByn = ceil($priceRub * $exchangeRate / 100 * (1 + $markupPercent / 100));
            return $priceByn . ' BYN';
        }

        return null;
    }

    /**
     * @return float
     */
    private function getExchangeRate(): float
    {
        $url = "https://api.nbrb.by/exrates/rates/456";
        $response = file_get_contents($url);

        if (!$response) {
            throw new RuntimeException('Не удалось получить курс валют');
        }

        $data = json_decode($response, true);

        if (!isset($data['Cur_OfficialRate'])) {
            throw new RuntimeException('Некорректный ответ от API курсов валют');
        }

        return (float)$data['Cur_OfficialRate'];
    }
}