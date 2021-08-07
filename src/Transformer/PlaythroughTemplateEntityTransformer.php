<?php
	namespace App\Transformer;

	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Transformer\Trait\PlaythroughCheckDataTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class PlaythroughTemplateEntityTransformer extends AbstractEntityTransformer {

		use PlaythroughCheckDataTrait;

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
		public function doCreateWork (): PlaythroughTemplate {

			assert($this->dto instanceof  PlaythroughTemplateDTO);

			$game = $this->gameRepository->find($this->dto->gameID);

			if (!$game) {
				throw new NotFoundHttpException('game not found');
			}

			return new PlaythroughTemplate($this->dto->name, $this->dto->description, $this->user, $game, $this->dto->visibility);

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return PlaythroughTemplate
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): PlaythroughTemplate {

			$playthroughTemplate = $this->repository->find($id);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->gameID = $playthroughTemplate->getGame()->getId();
			$this->validate($tempDTO);

			$playthroughTemplate = $this->checkAndSetData(json_decode($request->getContent(), true), $playthroughTemplate);

			Assert($playthroughTemplate instanceof PlaythroughTemplate);

			return $playthroughTemplate;

		}

	}