<?php
	namespace App\Command;

	use App\Service\IGDBHelper;
	use App\Transformer\GameEntityTransformer;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;
	use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

	class GameCreateCommand extends Command {

		protected static $defaultName = 'app:game:create';
		protected static $defaultDescription = 'This is for creating games to test the app with';

		/**
		 * @var IGDBHelper
		 */
		private IGDBHelper $IGDBHelper;

		/**
		 * @var EntityManagerInterface
		 */
		private EntityManagerInterface $entityManager;

		/**
		 * @var GameEntityTransformer
		 */
		private GameEntityTransformer $entityTransformer;

		public function __construct(IGDBHelper $IGDBHelper,
		                            EntityManagerInterface $entityManager,
		                            GameEntityTransformer $entityTransformer
		) {
			parent::__construct();
			$this->IGDBHelper = $IGDBHelper;
			$this->entityManager = $entityManager;
			$this->entityTransformer = $entityTransformer;
		}

		protected function configure() {
			$this->addArgument('igdb_id', InputArgument::REQUIRED,
				'this is the id for the game on Internet Game Database');

		}

		protected function execute(InputInterface $input, OutputInterface $output): int {

			try {

				$igdbDTO = $this->IGDBHelper->getGameFromIGDB($input->getArgument('igdb_id'));
				$game = $this->entityTransformer->assemble($igdbDTO);
				$this->entityManager->persist($game);
				$this->entityManager->flush();
				return ExitCode::OK;

			} catch (\Exception | TransportExceptionInterface) {
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
