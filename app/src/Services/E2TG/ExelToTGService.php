<?php

namespace App\Services\E2TG;

use App\Entity\TelegramGroup;
use App\Repository\TelegramGroupRepository;
use App\Services\Notification\Notifications\TelegramNotification;
use App\Services\Notification\NotificationService;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\File;
use Exception;
use RuntimeException;

readonly class ExelToTGService
{
    private const string TEMP_FILE_PREFIX = 'price_';
    private const string TEMP_FILE_EXTENSION = '.xlsx';
    private const string EXCHANGE_RATE_API_URL = 'https://api.nbrb.by/exrates/rates/456';
    private const string SPREADSHEET_URL = 'https://docs.google.com/spreadsheets/d/1clohCQ_K13d9PZDjhTdZgkO2n_jw05jpTW5W8o-LZhA/export?format=xlsx';
    private const int MARKUP_PERCENT = 30;
    private const int ROWS_TO_REMOVE = 4;
    private const string TELEGRAM_TEMPLATE = 'notification/telegram/current-shishka-exel.twig';

    /**
     * @param NotificationService $notificationService
     * @param TelegramGroupRepository $telegramGroupRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        private NotificationService     $notificationService,
        private TelegramGroupRepository $telegramGroupRepository,
        private LoggerInterface         $logger
    ) {
    }

    /**
     * @return void
     * @throws Exception
     */
    public function handleExcel(): void
    {
        try {
            $exchangeRate = $this->getExchangeRate();
            $fileContent = file_get_contents(self::SPREADSHEET_URL);

            if (!$fileContent) {
                throw new RuntimeException('Failed to upload a file from Google Sheets');
            }

            $tempFilePath = tempnam(sys_get_temp_dir(), self::TEMP_FILE_PREFIX) . self::TEMP_FILE_EXTENSION;
            file_put_contents($tempFilePath, $fileContent);

            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(false);
            $reader->setReadEmptyCells(false);

            $spreadsheet = $reader->load($tempFilePath);
            $sheet = $spreadsheet->getSheet(0);

            while ($spreadsheet->getSheetCount() > 1) {
                $spreadsheet->removeSheetByIndex(1);
            }

            $drawings = $sheet->getDrawingCollection();

            if (count($drawings) > 0) {
                $sheet->getDrawingCollection()->offsetUnset(0);
            }

            $sheet->removeRow(1, self::ROWS_TO_REMOVE);

            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(true);

                foreach ($cellIterator as $cell) {
                    $value = $cell->getValue();
                    $newValue = $this->convertPriceFormats($value, $exchangeRate);

                    if ($newValue !== null) {
                        $sheet->setCellValue($cell->getCoordinate(), $newValue);
                    }

                    unset($value, $cell);
                }
                unset($row, $cellIterator);
                gc_collect_cycles();
            }

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $spreadsheet->getActiveSheet()->getProtection()->setSheet(false);
            $writer->save($tempFilePath);

            $text = $this->notificationService->generateTelegramText(
                self::TELEGRAM_TEMPLATE,
                []
            );

            $group = $this->telegramGroupRepository->findByCode(TelegramGroup::CODE_GROUP_NOTIFY_CONTACT_FORM);

            $tgNotification = (new TelegramNotification())
                ->setGroup($group)
                ->setText($text)
                ->setAttachment(new File($tempFilePath));

            $this->notificationService->send([$tgNotification]);

        } catch (Exception $e) {
            $this->logger->error('Error when sending price list to Telegram: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @param string|null $value
     * @param float $exchangeRate
     * @return string|null
     */
    private function convertPriceFormats(?string $value, float $exchangeRate): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (preg_match('/(\d+)\s?Р/u', $value, $matches)) {
            $priceRub = (float)$matches[1];
            $priceByn = ceil($priceRub * $exchangeRate / 100 * (1 + self::MARKUP_PERCENT / 100));
            return $priceByn . ' BYN';
        }

        if (preg_match('/ОПТ ОТ (\d+) ТЫСЯЧ/ui', $value, $matches)) {
            $priceRub = (float)$matches[1] * 1000;
            $priceByn = ceil($priceRub * $exchangeRate / 100 * (1 + self::MARKUP_PERCENT / 100));
            return 'ОПТ ОТ ' . $priceByn . ' BYN';
        }

        if (preg_match('/(От \d+ штук-)(\d+)( рублей)/ui', $value, $matches)) {
            $priceRub = (float)$matches[2];
            $priceByn = ceil($priceRub * $exchangeRate / 100 * (1 + self::MARKUP_PERCENT / 100));
            return $matches[1] . $priceByn . $matches[3];
        }

        if (preg_match('/(\d+)( рублей)/ui', $value, $matches)) {
            $priceRub = (float)$matches[1];
            $priceByn = ceil($priceRub * $exchangeRate / 100 * (1 + self::MARKUP_PERCENT / 100));
            return $priceByn . ' BYN';
        }

        return null;
    }

    /**
     * @return float
     */
    private function getExchangeRate(): float
    {
        $response = file_get_contents(self::EXCHANGE_RATE_API_URL);

        if (!$response) {
            throw new RuntimeException('Failed to get the exchange rate');
        }

        $data = json_decode($response, true);

        if (!isset($data['Cur_OfficialRate'])) {
            throw new RuntimeException('Incorrect response from currency rates API');
        }

        return (float)$data['Cur_OfficialRate'];
    }
}