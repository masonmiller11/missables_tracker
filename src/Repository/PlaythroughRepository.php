<?php
	namespace App\Repository;

	use App\Entity\Playthrough\Playthrough;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;
	use JetBrains\PhpStorm\ArrayShape;

	/**
		 * @method Playthrough|null find($id, $lockMode = null, $lockVersion = null)
		 * @method Playthrough|null findOneBy(array $criteria, array $orderBy = null)
		 * @method Playthrough[]    findAll()
		 * @method Playthrough[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class PlaythroughRepository extends AbstractBaseRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, Playthrough::class);
		}

		/**
		 * @param int $ownerId
		 * @param int $page
		 * @param int $pageSize
		 * @return array|null
		 */
		#[ArrayShape(['items' => "array", 'totalItems' => "int", 'pageCount' => "float"])]
		public function findAllByOwner(int $ownerId, int $page, int $pageSize): array|null {
			$qb = $this->createQueryBuilder('playthrough')
				->select('playthrough')
				->andWhere('owner.id = :ownerId')
				->leftJoin('playthrough.owner', 'owner')
				->setParameter('ownerId', $ownerId);

			return $this->doPagination($qb, $page, $pageSize, 'playthroughs');

		}

		#[ArrayShape(['items' => "array", 'totalItems' => "int", 'pageCount' => "float"])]
		public function findAllByTemplate(int $templateId, int $page, int $pageSize): array|null {
			$qb = $this->createQueryBuilder('playthrough')
				->select('playthrough')
				->andWhere('playthrough.templateId = :templateId')
				->setParameter('templateId', $templateId);

			return $this->doPagination($qb, $page, $pageSize, 'playthroughs');

		}

	}