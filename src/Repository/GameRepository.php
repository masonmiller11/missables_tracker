<?php
	namespace App\Repository;

	use App\Entity\Game;
	use Doctrine\ORM\NonUniqueResultException;
	use Doctrine\Persistence\ManagerRegistry;
	use JetBrains\PhpStorm\ArrayShape;

	/**
		 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
		 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
		 * @method Game[]    findAll()
		 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class GameRepository extends AbstractBaseRepository {

		public function __construct(ManagerRegistry $registry) {
			parent::__construct($registry, Game::class);
		}

		/**
		 * @throws NonUniqueResultException
		 */
		public function findGameByInternetGameDatabaseID (int $internetGameDatabaseID): Game | null {
			$qb = $this->createQueryBuilder('game')
				->andWhere('game.internetGameDatabaseID = :internetGameDatabaseID')
				->setParameter('internetGameDatabaseID', $internetGameDatabaseID);

			return $qb->getQuery()->getOneOrNullResult();
		}

		public function searchByName (string $term): array | null {
			$qb = $this->createQueryBuilder('game')
				->andWhere('game.title LIKE :searchTerm')
				->setParameter('searchTerm','%' . $term . '%');

			return $this->doPagination($qb, $page, $pageSize, 'games');
		}

		/**
		 * @param int $page
		 * @param int $pageSize
		 * @return array|null
		 */
		#[ArrayShape(['items' => "array", 'totalItems' => "int", 'pageCount' => "float"])]
		public function findAllOrderByTemplates (int $page, int $pageSize): array | null {
			$qb = $this->createQueryBuilder('game')
				->select('COUNT(template) AS HIDDEN myCount', 'game')
				->leftJoin('game.playthroughTemplates', 'template')
				->orderBy('myCount', 'DESC')
				->groupBy('game');

			return $this->doPagination($qb, $page, $pageSize, 'games');
		}

	}