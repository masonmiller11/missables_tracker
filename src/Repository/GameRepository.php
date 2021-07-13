<?php
	namespace App\Repository;

	use App\Entity\Game;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\ORM\NonUniqueResultException;
	use Doctrine\Persistence\ManagerRegistry;

	/**
		 * @method Game|null find($id, $lockMode = null, $lockVersion = null)
		 * @method Game|null findOneBy(array $criteria, array $orderBy = null)
		 * @method Game[]    findAll()
		 * @method Game[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class GameRepository extends ServiceEntityRepository {

		public function __construct(ManagerRegistry $registry)
		{
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

			return $qb->getQuery()->getResult();
		}

		public function topTenByNumberOfTemplates (): array | null {
			$qb = $this->createQueryBuilder('game')
				->select('COUNT(template) AS HIDDEN myCount', 'game')
				->leftJoin('game.playthroughTemplates', 'template')
				->orderBy('myCount', 'DESC')
				->groupBy('game')
				->setMaxResults(10);

			return $qb->getQuery()->getResult();
		}

	}