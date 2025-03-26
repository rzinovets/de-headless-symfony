<?php

namespace App\Command;

use App\Entity\FAQ;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFAQCommand extends Command
{
    protected static $defaultName = 'app:generate-faq';

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $faker = Factory::create();

        for ($i = 0; $i < 100; $i++) {
            $faq = new FAQ();
            $faq->setQuestion($faker->sentence(10));
            $faq->setAnswer($faker->paragraphs(3, true));
            $faq->setPriority($faker->optional(0.7)->numberBetween(1, 10));

            $this->entityManager->persist($faq);

            if ($i % 20 === 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();
        $output->writeln('Generated 100 FAQ entries.');

        return Command::SUCCESS;
    }
}