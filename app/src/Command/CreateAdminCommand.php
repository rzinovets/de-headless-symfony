<?php

namespace App\Command;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin user.',
    aliases: ['app:add-admin'],
    hidden: false
)]
class CreateAdminCommand extends Command
{
    /**
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordHasherInterface $passwordHasher
     * @param LoggerInterface $logger
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::OPTIONAL, 'The username of the admin.')
            ->addArgument('password', InputArgument::OPTIONAL, 'The password of the admin.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🎨✨ Admin Creator');
        $io->section('🚀 Creating a new admin user');

        $username = $input->getArgument('username') ?? $io->ask('🖋️ Enter the username', null, static fn(string $value) =>
        trim($value) !== '' ? $value : throw new RuntimeException('⛔ Username cannot be empty.'));

        $existingUser = $this->entityManager->getRepository(Admin::class)->findOneBy(['username' => $username]);
        if ($existingUser) {
            $io->error('❌ User with this username already exists.');
            return Command::FAILURE;
        }

        $password = $input->getArgument('password') ?? $io->askQuestion(
            (new Question('🔑 Enter the password'))
                ->setHidden(true)
                ->setValidator(static fn(string $value) =>
                strlen($value) >= 8 ? $value : throw new RuntimeException('⛔ Password must be at least 8 characters long.'))
        );

        $admin = (new Admin())
            ->setUsername($username)
            ->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($admin, $password);
        $admin->setPassword($hashedPassword);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();

        $this->logger->info('📝 Admin user created: ' . $username);

        $io->success('🎉 Admin user successfully created!');

        return Command::SUCCESS;
    }
}