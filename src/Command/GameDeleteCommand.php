<?php
	namespace App\Command;

	use App\Entity\Game;
	use App\Repository\GameRepository;
	use DateTimeImmutable;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

	class GameDeleteCommand extends Command {

		protected static $defaultName = 'app:game:delete';
		protected static $defaultDescription = 'This is for removing games from the database';

		/**
		 * @var EntityManagerInterface
		 */
		private EntityManagerInterface $entityManager;

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		public function __construct (EntityManagerInterface $entityManager,
									 GameRepository $gameRepository) {
			parent::__construct();
			$this->entityManager = $entityManager;
			$this->gameRepository = $gameRepository;
		}

		protected function configure() {
			$this->addArgument('id', InputArgument::REQUIRED,
				'this is the game\'s id');

		}

		protected function execute(InputInterface $input, OutputInterface $output) :int {

			$game = $this->gameRepository->find($input->getArgument('id'));

			$this->entityManager->remove($game);

			$this->entityManager->flush();

			return ExitCode::OK;
		}

		protected function interact(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			if (!$input->getArgument('id')) {
				$input->setArgument('id', $io->ask(
					'What is the id of the game you are trying to delete?'
				));
			}
		}
	}
