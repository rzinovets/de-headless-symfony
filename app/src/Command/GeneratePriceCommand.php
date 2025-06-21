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

        $output->writeln("");
        $output->writeln("<fg=blue;options=bold>=== Price List Generator ===</>");
        $output->writeln("<fg=blue>Processing price list from Google Sheets to Telegram</>");
        $output->writeln("");

        try {
            $output->write("<fg=yellow>Processing...</> ");

            $this->exelToTGService->handleExcel();

            $output->write("\x0D");
            $output->write("\x1B[2K");

            $output->writeln("<fg=green>✔ Price list successfully processed and sent to Telegram</>");
            $output->writeln("<fg=green>✔ Memory usage: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . "MB</>");
            $output->writeln("");
            $output->writeln("<fg=blue;options=bold>Operation completed successfully!</>");
            $output->writeln("");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln("");
            $output->writeln("<fg=red;options=bold>✖ Error occurred:</>");
            $output->writeln("<fg=red>  " . $e->getMessage() . "</>");
            $output->writeln("");
            $output->writeln("<bg=red;fg=white>                </>");
            $output->writeln("<bg=red;fg=white>  PROCESS FAILED  </>");
            $output->writeln("<bg=red;fg=white>                </>");
            $output->writeln("");

            return Command::FAILURE;
        } finally {
            ini_restore('memory_limit');
        }
    }
}