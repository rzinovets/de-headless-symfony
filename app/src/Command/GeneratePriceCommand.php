<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;
use App\Services\E2TG\ExelToTGService;

class GeneratePriceCommand extends Command
{
    protected static $defaultName = 'app:generate-price';
    private const string MEMORY_LIMIT = '512M';

    /**
     * @param ExelToTGService $exelToTGService
     */
    public function __construct(
        private readonly ExelToTGService $exelToTGService
    ) {
        parent::__construct();
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
        ini_set('memory_limit', self::MEMORY_LIMIT);

        try {
            $this->exelToTGService->handleExcel();
            $output->writeln('Price list successfully processed and sent');
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        } finally {
            ini_restore('memory_limit');
        }
    }
}