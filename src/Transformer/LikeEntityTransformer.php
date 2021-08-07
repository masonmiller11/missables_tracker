<?php
	namespace App\Transformer;

	use App\DTO\Like\LikeDTO;
	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\PlaythroughTemplateLike;
	use App\Repository\LikeRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\UserRepository;
	use Doctrine\DBAL\Exception;
	use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	class LikeEntityTransformer extends AbstractEntityTransformer {

		private PlaythroughTemplateRepository $playthroughTemplateRepository;

		#[Pure]
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,
		                            LikeRepository $likeRepository,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository) {

			parent::__construct($entityManager, $validator);
			$this->repository = $likeRepository;
			$this->playthroughTemplateRepository = $playthroughTemplateRepository;

		}

		/**
		 * @throws Exception
		 */
		public function doCreateWork(): PlaythroughTemplateLike {

			assert($this->dto instanceof LikeDTO);

			$getLikeIfExists = $this->repository->getLikeByUserAndTemplate($this->user->getId(), $this->dto->templateID);

			if ($getLikeIfExists) {
				throw new Exception('a user cannot like one template more than once');
			}

			$template = $this->playthroughTemplateRepository->find($this->dto->templateID);

			return new PlaythroughTemplateLike($this->user, $template);

		}

		public function doUpdateWork(int $id, Request $request, bool $skipValidation): EntityInterface {
			// no op
		}

	}