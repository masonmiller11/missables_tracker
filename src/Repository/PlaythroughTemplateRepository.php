<?php
	namespace App\Repository;

	use App\Entity\Playthrough\PlaythroughTemplate;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;

		/**
		 * @method PlaythroughTemplate|null find($id, $lockMode = null, $lockVersion = null)
		 * @method PlaythroughTemplate|null findOneBy(array $criteria, array $orderBy = null)
		 * @method PlaythroughTemplate[]    findAll()
		 * @method PlaythroughTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class PlaythroughTemplateRepository extends ServiceEntityRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, PlaythroughTemplate::class);
		}

		public function findByGame(int $gameId) {
			$qb = $this->createQueryBuilder('template')
				->select('COUNT(user) AS HIDDEN myCount', 'template')
				->andWhere('game.id = :gameId')
				->andWhere('template.visibility = true')
				->leftJoin('template.game', 'game')
				->leftJoin('template.likedBy','user')
				->orderBy('myCount', 'DESC')
				->setParameter('gameId', $gameId);

			$query = $qb->getQuery();

			return $query->execute();
		}

		public function findByAuthor(int $authorId) {
			$qb = $this->createQueryBuilder('template')
				->select('COUNT(user) AS HIDDEN myCount', 'template')
				->andWhere('author.id = :authorId')
				->andWhere('template.visibility = true')
				->leftJoin('template.owner', 'author')
				->leftJoin('template.likedBy','user')
				->orderBy('myCount', 'DESC')
				->setParameter('authorId', $authorId);

			$query = $qb->getQuery();

			return $query->execute();
		}

	}