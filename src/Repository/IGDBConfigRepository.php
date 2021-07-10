<?php
	namespace App\Repository;

	use App\Entity\IGDBConfig;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;

	/**
	 * @method IGDBConfig|null find($id, $lockMode = null, $lockVersion = null)
	 * @method IGDBConfig|null findOneBy(array $criteria, array $orderBy = null)
	 * @method IGDBConfig[]    findAll()
	 * @method IGDBConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
	 */
	class IGDBConfigRepository extends ServiceEntityRepository {

		public function __construct(ManagerRegistry $registry) {
			parent::__construct($registry, IGDBConfig::class);
		}

	}