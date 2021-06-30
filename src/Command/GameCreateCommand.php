<?php
	namespace App\Command;

	use App\Entity\Game;
	use DateTimeImmutable;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

	class GameCreateCommand extends Command {

		protected static $defaultName = 'app:game:create';
		protected static $defaultDescription = 'This is for creating games to test the app with';

		private EntityManagerInterface $entityManager;

		public function __construct (EntityManagerInterface $entityManager) {
			parent::__construct();
			$this->entityManager = $entityManager;
		}

		protected function configure() {
			$this->addArgument('title', InputArgument::REQUIRED,
				'this is the game\'s title');
			$this->addArgument('release', InputArgument::REQUIRED,
				'this is the game\'s release date in Y-m-d');
			$this->addArgument('genre', InputArgument::REQUIRED,
				'this is the genre that this game belongs to');
			$this->addArgument('developer', InputArgument::REQUIRED,
				'this is the developer who made the game');
		}

		protected function execute(InputInterface $input, OutputInterface $output) :int {

			$game = new Game(
				$input->getArgument('genre'),
				$input->getArgument('title'),
				$input->getArgument('developer'),
				DateTimeImmutable::createFromFormat('Y-m-d',$input->getArgument('release'))
			);

			$this->entityManager->persist($game);
			$this->entityManager->flush();

			return ExitCode::OK;
		}

		protected function interact(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			if (!$input->getArgument('title')) {
				$input->setArgument('title', $io->ask(
					'What is the title of the game you are trying to add?'
				));
			}
			if (!$input->getArgument('genre')) {
				$input->setArgument('genre', $io->ask(
					'What is the genre of the game you are trying to add?'
				));
			}
			if (!$input->getArgument('release')) {
				$input->setArgument('release', $io->ask(
					'What is the release date of the game you are trying to add? om Y-m-d?'
				));
			}
			if (!$input->getArgument('developer')) {
				$input->setArgument('developer', $io->ask(
					'What company or person was responsible for developing the game?'
				));
			}
		}
	}
