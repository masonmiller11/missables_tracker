<?php
	namespace App\Repository;

	use App\Entity\Playthrough\PlaythroughTemplate;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\ORM\Tools\Pagination\Paginator;
	use Doctrine\Persistence\ManagerRegistry;
	use JetBrains\PhpStorm\ArrayShape;

	/**
		 * @method PlaythroughTemplate|null find($id, $lockMode = null, $lockVersion = null)
		 * @method PlaythroughTemplate|null findOneBy(array $criteria, array $orderBy = null)
		 * @method PlaythroughTemplate[]    findAll()
		 * @method PlaythroughTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class PlaythroughTemplateRepository extends AbstractBaseRepository {
		public function __construct(ManagerRegistry $registry) {
			parent::__construct($registry, PlaythroughTemplate::class);
		}

		/**
		 * @param int $gameId
		 * @param int $page
		 * @param int $pageSize
		 * @return array|null
		 */
		#[ArrayShape(['items' => "array", 'totalItems' => "int", 'pageCount' => "float"])]
		public function findAllByGame(int $gameId, int $page, int $pageSize): array|null {
			$qb = $this->createQueryBuilder('template')
				->select('template')
				->andWhere('game.id = :gameId')
				->andWhere('template.visibility = true')
				->leftJoin('template.game', 'game')
				->orderBy('template.numberOfLikes', 'DESC')
				->setParameter('gameId', $gameId);

			return $this->doPagination($qb, $page, $pageSize, 'templates');

		}

		/**
		 * @param int $authorId
		 * @param int $page
		 * @param int $pageSize
		 * @return array|null
		 */
		#[ArrayShape(['items' => "array", 'totalItems' => "int", 'pageCount' => "float"])]
		public function findAllByAuthor(int $authorId, int $page, int $pageSize): array|null {
			$qb = $this->createQueryBuilder('template')
				->select('template')
				->andWhere('author.id = :authorId')
				->andWhere('template.visibility = true')
				->leftJoin('template.owner', 'author')
				->orderBy('template.numberOfLikes', 'DESC')
				->setParameter('authorId', $authorId);

			return $this->doPagination($qb, $page, $pageSize, 'templates');

		}

		/**
		 * @param int $page
		 * @param int $pageSize
		 * @return array|null
		 */
		#[ArrayShape(['items' => "array", 'totalItems' => "int", 'pageCount' => "float"])]
		public function findAllOrderByLikes(int $page, int $pageSize): array|null {
			$qb = $this->createQueryBuilder('template')
				->addSelect('template')
				->andWhere('template.visibility = true')
				->orderBy('template.numberOfLikes', 'DESC');

			return $this->doPagination($qb, $page, $pageSize, 'templates');

		}
	}