<?php
	namespace App\Repository;

	use App\Entity\Playthrough\PlaythroughTemplateLike;
	use Doctrine\Persistence\ManagerRegistry;
	use JetBrains\PhpStorm\ArrayShape;

	/**
		 * @method PlaythroughTemplateLike|null find($id, $lockMode = null, $lockVersion = null)
		 * @method PlaythroughTemplateLike|null findOneBy(array $criteria, array $orderBy = null)
		 * @method PlaythroughTemplateLike[]    findAll()
		 * @method PlaythroughTemplateLike[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class LikeRepository extends AbstractBaseRepository
	{
		public function __construct(ManagerRegistry $registry) {
			parent::__construct($registry, PlaythroughTemplateLike::class);
		}

		/**
		 * @param $userId
		 * @param $templateId
		 * @return array|null
		 */
		public function getLikeByUserAndTemplate($userId, $templateId): array | null {
			$qb = $this->createQueryBuilder('templateLike')
				->select('templateLike')
				->andWhere('user.id = :userId')
				->andWhere('template.id = :templateId')
				->leftJoin('templateLike.likedTemplate', 'template')
				->leftJoin('templateLike.likedBy', 'user')
				->setParameter('userId', $userId)
				->setParameter('templateId', $templateId);

			return $qb->getQuery()->getResult();

		}

		/**
		 * @param int $ownerId
		 * @param int $page
		 * @param int $pageSize
		 * @return array|null
		 */
		#[ArrayShape(['items' => "array", 'totalItems' => "int", 'pageCount' => "float"])]
		public function findAllByOwner(int $ownerId, int $page, int $pageSize): array|null {
			$qb = $this->createQueryBuilder('templateLike')
				->select('templateLike')
				->andWhere('owner.id = :ownerId')
				->leftJoin('templateLike.likedBy', 'owner')
				->setParameter('ownerId', $ownerId);

			return $this->doPagination($qb, $page, $pageSize, 'favorites');

		}

	}