<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin.',
    aliases: ['app:add-admin'],
    hidden: false
)]
class CreateAdminCommand extends Command
{
    protected static $defaultDescription = 'Creates a new admin.';

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'The username of the user.')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Admin Creator',
            '============',
            '',
        ]);

        $output->writeln('Hello: '.$input->getArgument('username'));

        $admin = new \App\Entity\Admin();
        $em = $this->entityManager;

        $admin->setUsername($input->getArgument('username'))
            ->setPassword($input->getArgument('password'))
            ->setRoles(['ROLE_ADMIN']);

        $em->persist($admin);
        $em->flush();

        return Command::SUCCESS;
    }
}