<?php
	namespace App\Transformer;

	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughRequestDTOTransformer;
	use App\Entity\Game;
	use App\Entity\Playthrough\Playthrough;
	use App\Exception\ValidationException;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Request\Payloads\PlaythroughPayload;
	use App\Transformer\Trait\PlaythroughTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class PlaythroughEntityTransformer extends AbstractEntityTransformer {

		use PlaythroughTrait;

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var PlaythroughTemplateRepository
		 */
		private PlaythroughTemplateRepository $playthroughTemplateRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 *
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 * @param GameRepository $gameRepository
		 * @param PlaythroughRequestDTOTransformer $DTOTransformer
		 * @param PlaythroughRepository $playthroughRepository
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
		                            GameRepository $gameRepository,
		                            PlaythroughRequestDTOTransformer $DTOTransformer,
		                            PlaythroughRepository $playthroughRepository,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository) {

			parent::__construct($entityManager, $validator);

			$this->gameRepository = $gameRepository;
			$this->DTOTransformer = $DTOTransformer;
			$this->playthroughTemplateRepository = $playthroughTemplateRepository;
			$this->repository = $playthroughRepository;

		}

		/**
		 *
		 * @return Playthrough
		 */
		public function doCreateWork(): Playthrough {

			if (!($this->dto instanceof PlaythroughPayload)) {
				throw new \InvalidArgumentException(
					'PlaythroughEntityTransformer\'s Payload not instance of PlaythroughPayload'
				);
			}

			$game = $this->getGame();
			$this->doesTemplateExist();

			return new Playthrough(
				$this->dto->name,
				$this->dto->description,
				$game,
				$this->dto->templateId,
				$this->user,
				$this->dto->visibility
			);

		}



		private function doesTemplateExist(): void {
			$template = $this->playthroughTemplateRepository->find($this->dto->templateId);

			if (!$template) {
				throw new NotFoundHttpException('template not found');
			}
		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return Playthrough
		 * @throws ValidationException
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): Playthrough {

			$playthrough = $this->repository->find($id);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->gameID = $playthrough->getGame()->getId();
			$tempDTO->templateId = $playthrough->getTemplateId();

			if (!$skipValidation) $this->validate($tempDTO);

			$playthrough = $this->checkAndSetData(json_decode($request->getContent(), true), $playthrough);


			if (!($playthrough instanceof Playthrough)) {
				throw new \InvalidArgumentException(
					$playthrough::class . ' not instance of Playthrough. Does ' . $id . 'belong to a playthrough?');
			}

			return $playthrough;

		}

	}