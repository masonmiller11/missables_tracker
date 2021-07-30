<?php
	namespace App\Command;

	use App\Entity\User;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

	class UserCreateCommand extends Command {

		protected static $defaultName = 'app:user:create';
		protected static $defaultDescription = 'This is for creating users to test the app with';

		private EntityManagerInterface $entityManager;
		private UserPasswordHasherInterface $encoder;

		public function __construct (EntityManagerInterface $entityManager, UserPasswordHasherInterface $encoder) {
			parent::__construct();
			$this->entityManager = $entityManager;
			$this->encoder = $encoder;
		}

		protected function configure() {
			$this->addArgument('email', InputArgument::REQUIRED, 'this is a user\'s email address');
			$this->addOption('password', 'p', InputOption::VALUE_NONE,'this is a user\'s password');
		}

		protected function execute(InputInterface $input, OutputInterface $output) :int {

			$user = new User($input->getArgument('email'),'testusername');

			if ($password = $input->getOption('password')) {

				$password = $this->encoder->hashPassword($user,$password);
				$user->setPassword($password);

			}

			$this->entityManager->persist($user);
			$this->entityManager->flush();

			return ExitCode::OK;
		}

		protected function interact(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			if (!$input->getArgument('email')) {
				$input->setArgument('email', $io->ask('What is the email of the test user?'));
			}
			if ($input->getOption('password')) {
				$input->setOption('password', $io->askHidden('What is the user\'s password going to be?'));
			}
		}
	}
