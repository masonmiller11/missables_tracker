<?php
	namespace App\Command;

	use App\Entity\Genre;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Style\SymfonyStyle;

	class GenreCreateCommand extends Command {

		protected static $defaultName = 'app:genre:create';
		protected static $defaultDescription = 'This is for creating genres';

		private EntityManagerInterface $entityManager;

		public function __construct (EntityManagerInterface $entityManager) {
			parent::__construct();
			$this->entityManager = $entityManager;
		}

		protected function configure() {
			$this->addArgument('name', InputArgument::REQUIRED, 'this is the genre\'s name');
		}

		protected function execute(InputInterface $input, OutputInterface $output) :int {

			$genre = new Genre($input->getArgument('name'));

			$this->entityManager->persist($genre);
			$this->entityManager->flush();

			return ExitCode::OK;
		}

		protected function interact(InputInterface $input, OutputInterface $output) {
			$io = new SymfonyStyle($input, $output);
			if (!$input->getArgument('name')) {
				$input->setArgument('name', $io->ask('What is the name of this genre?'));
			}
		}
	}
