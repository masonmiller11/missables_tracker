<?php
	namespace App\Repository;

	use App\Entity\PlaythroughTemplate;
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
				->andWhere('game.id = :gameId')
				->andWhere('template.visibility = true')
				->leftJoin('template.game', 'game')
				->setParameter('gameId', $gameId)
				->addOrderBy('template.votes', 'DESC');

			$query = $qb->getQuery();

			return $query->execute();
		}

		public function findByAuthor(int $authorId) {
			$qb = $this->createQueryBuilder('template')
				->andWhere('author.id = :authorId')
				->andWhere('template.visibility = true')
				->leftJoin('template.owner', 'author')
				->setParameter('authorId', $authorId)
				->addOrderBy('template.votes', 'DESC');

			$query = $qb->getQuery();

			return $query->execute();
		}

	}