<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'ludo:user:create',
    description: 'Create users',
)]
class LudoUserCreateCommand extends Command
{
    private EntityManagerInterface $em;

    private UserPasswordHasherInterface $hasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $hasher, string $name = null)
    {
        parent::__construct($name);

        $this->em = $em;
        $this->hasher = $hasher;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'User name')
            ->addArgument('username', InputArgument::REQUIRED, 'User username')
            ->addArgument('password', InputArgument::REQUIRED, 'User password')
            ->addOption('super', 's', InputOption::VALUE_NONE, 'Make super user');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        if (null !== $name && null !== $username && null !== $password) {
            return;
        }

        $io = new SymfonyStyle($input, $output);

        $io->title('Interactive wizard for new security user creation');
        $io->text([
            'If you prefer not to use this interactive wizard,',
            'just provide the required arguments in the given order:',
            '<question>$ bin/console ludo:user:create <name> <username> <password></question>',
            'You always will be asked for the missing arguments.',
        ]);
        $io->newLine();

        // ask for the <name> if missing
        if (null !== $name) {
            $io->text('<info>User name</info>: ' . $name);
        } else {
            $name = $io->ask('User name');
            $input->setArgument('name', $name);
        }

        // ask for the <username> if missing
        if (null !== $username) {
            $io->text('<info>User username</info>: ' . $username);
        } else {
            $username = $io->ask('User username');
            $input->setArgument('username', $username);
        }

        // ask for the <password> if missing
        if (null !== $password) {
            $io->text('<info>User password</info>: ' . $password);
        } else {
            $password = $io->ask('User password');
            $input->setArgument('password', $password);
        }
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        // read arguments and options
        $name = $input->getArgument('name');
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $super = $input->getOption('super');

        // confirmation
        if ($name) {
            $io->note(sprintf('Name: %s', $name));
        }
        if ($username) {
            $io->note(sprintf('Username: %s', $username));
        }
        if ($password) {
            $io->note(sprintf('Password: %s', $password));
        }
        $confirm = $io->confirm('Proceed?');
        if (!$confirm) {
            return 0;
        }

        // check if user already exists
        $user = $this->em->getRepository(User::class)->findOneBy(['username' => $username]);
        if ($user) {
            $io->error(sprintf("There's an user with username %s", $username));
            return 0;
        }

        // create user
        $user = new User();
        $user
            ->setName($name)
            ->setUsername($username);

        // hash password
        $user->setPassword($this->hasher->hashPassword($user, $password));

        // super admin
        if ($super) {
            $user->addRole('ROLE_SUPER');
        }

        // persist to db
        $this->em->persist($user);
        $this->em->flush();


        $io->success('The user has been successfully created!');

        return Command::SUCCESS;
    }
}
