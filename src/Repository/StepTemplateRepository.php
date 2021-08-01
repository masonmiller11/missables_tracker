<?php
	namespace App\Repository;

	use App\Entity\Step\Step;
	use App\Entity\Step\StepTemplate;
	use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
	use Doctrine\Persistence\ManagerRegistry;

		/**
		 * @method StepTemplate|null find($id, $lockMode = null, $lockVersion = null)
		 * @method StepTemplate|null findOneBy(array $criteria, array $orderBy = null)
		 * @method StepTemplate[]    findAll()
		 * @method StepTemplate[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
		 */
	class StepTemplateRepository extends AbstractBaseRepository
	{
		public function __construct(ManagerRegistry $registry)
		{
			parent::__construct($registry, StepTemplate::class);
		}

	}