<?php
	namespace App\Transformer;

	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Exception\ValidationException;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Request\Payloads\PlaythroughTemplatePayload;
	use App\Transformer\Trait\PlaythroughTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class PlaythroughTemplateEntityTransformer extends AbstractEntityTransformer {

		use PlaythroughTrait;

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * PlaythroughTemplateEntityTransformer constructor.
		 * @param EntityManagerInterface $entityManager
		 * @param ValidatorInterface $validator
		 * @param GameRepository $gameRepository
		 * @param PlaythroughTemplateRequestDTOTransformer $DTOTransformer
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator,
		                            GameRepository $gameRepository,
		                            PlaythroughTemplateRequestDTOTransformer $DTOTransformer,
		                            PlaythroughTemplateRepository $playthroughTemplateRepository) {

			parent::__construct($entityManager, $validator);

			$this->gameRepository = $gameRepository;
			$this->DTOTransformer = $DTOTransformer;
			$this->repository = $playthroughTemplateRepository;

		}

		/**
		 *
		 * @return PlaythroughTemplate
		 */
		public function doCreateWork(): PlaythroughTemplate {

			if (!($this->dto instanceof PlaythroughTemplatePayload)) {
				throw new \InvalidArgumentException(
					'PlaythroughTemplateEntityTransformer\'s Payload not instance of PlaythroughTemplatePayload'
				);
			}

			$game = $this->getGame();

			return new PlaythroughTemplate($this->dto->name, $this->dto->description, $this->user, $game, $this->dto->visibility);

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return PlaythroughTemplate
		 * @throws ValidationException
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): PlaythroughTemplate {

			$playthroughTemplate = $this->repository->find($id);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->gameID = $playthroughTemplate->getGame()->getId();
			$this->validate($tempDTO);

			$playthroughTemplate = $this->checkAndSetData(json_decode($request->getContent(), true),
				$playthroughTemplate);

			if (!($playthroughTemplate instanceof PlaythroughTemplate)) {
				throw new \InvalidArgumentException(
					$playthroughTemplate::class . ' not instance of PlaythroughTemplate. Does ' . $id .
					'belong to a playthrough template?'
				);
			}

			return $playthroughTemplate;

		}

	}