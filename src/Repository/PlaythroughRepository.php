<?php
	namespace App\Repository;

	use App\Entity\Playthrough\Playthrough;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;

		/**
		 * @method Playthrough|null find($id, $lockMode = null, $lockVersion = null)
		 * @method Playthrough|null findOneBy(array $criteria, array $orderBy = null)
		 * @method Playthrough[]    findAll()
		 * @method Playthrough[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class PlaythroughRepository extends ServiceEntityRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, Playthrough::class);
		}

	}