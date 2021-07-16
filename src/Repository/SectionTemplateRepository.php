<?php
	namespace App\Repository;

	use App\Entity\Section\SectionTemplate;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;

		/**
		 * @method SectionTemplate|null find($id, $lockMode = null, $lockVersion = null)
		 * @method SectionTemplate|null findOneBy(array $criteria, array $orderBy = null)
		 * @method SectionTemplate[]    findAll()
		 * @method SectionTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class SectionTemplateRepository extends ServiceEntityRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, SectionTemplate::class);
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