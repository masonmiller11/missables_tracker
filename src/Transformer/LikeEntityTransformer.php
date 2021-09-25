<?php
	namespace App\Transformer;

	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Playthrough\PlaythroughTemplateLike;
	use App\Exception\DuplicateLikeException;
	use App\Repository\LikeRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Request\Payloads\LikePayload;
	use App\Request\Payloads\PayloadInterface;
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
		 * @throws DuplicateLikeException
		 */
		public function doCreateWork(): PlaythroughTemplateLike {

			if (!($this->dto instanceof LikePayload)) {
				throw new \InvalidArgumentException('GameEntityTransformer\'s DTO not instance of AbstractGameDTO');
			}

			$this->doesLikeAlreadyExist();

			$template = $this->getTemplate();

			return new PlaythroughTemplateLike($this->user, $template);

		}

		/**
		 * @throws DuplicateLikeException
		 */
		private function doesLikeAlreadyExist(): void {
			if ($this->repository->getLikeByUserAndTemplate($this->user->getId(), $this->dto->templateId))
				throw new DuplicateLikeException();
		}

		private function getTemplate(): PlaythroughTemplate {
			$template = $this->playthroughTemplateRepository->find($this->dto->templateId);

			if (!$template)
				throw new \InvalidArgumentException('A template with this id could not be found');

			return $template;
		}

		public function doUpdateWork(): EntityInterface {
			// no op
		}

	}