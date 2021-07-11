<?php
	namespace App\Command;

	use App\Entity\Game;
	use App\Service\IGDBHelper;
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

		private IGDBHelper $IGDBHelper;

		public function __construct (EntityManagerInterface $entityManager,
									 IGDBHelper $IGDBHelper) {
			parent::__construct();
			$this->entityManager = $entityManager;
			$this->IGDBHelper = $IGDBHelper;
		}

		protected function configure() {
			$this->addArgument('igdb_id', InputArgument::REQUIRED,
				'this is the id for the game on Internet Game Database');

		}

		protected function execute(InputInterface $input, OutputInterface $output) :int {

			try {

				$this->IGDBHelper->getGameAndSave($input->getArgument('igdb_id'));
				return ExitCode::OK;

			} catch(\Exception) {
				return ExitCode::ERROR;
			}

		}

		protected function interact(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			if (!$input->getArgument('igdb_id')) {
				$input->setArgument('igdb_id', $io->ask(
					'What is the id of the game on the Internet Game Database?'
				));
			}
		}
	}
