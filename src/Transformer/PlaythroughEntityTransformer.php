<?php
	namespace App\Transformer;

	use App\DTO\Playthrough\PlaythroughDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughRequestDTOTransformer;
	use App\Entity\Playthrough\Playthrough;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Transformer\Trait\PlaythroughCheckDataTrait;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class PlaythroughEntityTransformer extends AbstractEntityTransformer {

		use PlaythroughCheckDataTrait;

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
		 * @param EntityManagerInterface           $entityManager
		 * @param ValidatorInterface               $validator
		 * @param GameRepository                   $gameRepository
		 * @param PlaythroughRequestDTOTransformer $DTOTransformer
		 * @param PlaythroughRepository            $playthroughRepository
		 * @param PlaythroughTemplateRepository    $playthroughTemplateRepository
		 */
		#[Pure]
		public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator,
		                            GameRepository $gameRepository, PlaythroughRequestDTOTransformer $DTOTransformer,
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
		public function doCreateWork (): Playthrough {

			assert($this->dto instanceof PlaythroughDTO);

			$game = $this->gameRepository->find($this->dto->gameID);

			if (!$game) {
				throw new NotFoundHttpException('game not found');
			}

			$template = $this->playthroughTemplateRepository->find($this->dto->templateId);

			if (!$template) {
				throw new NotFoundHttpException('template not found');
			}

			return new Playthrough($this->dto->name, $this->dto->description, $game, $this->dto->templateId, $this->user, $this->dto->visibility);

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return Playthrough
		 */
		public function doUpdateWork(int $id, Request $request, bool $skipValidation = false): Playthrough {

			$playthrough = $this->repository->find($id);

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$tempDTO->gameID = $playthrough->getGame()->getId();
			$tempDTO->templateId = $playthrough->getTemplateId();
			$this->validate($tempDTO);

			$playthrough = $this->checkData(json_decode($request->getContent(), true), $playthrough);

			Assert($playthrough instanceof Playthrough);

			return $playthrough;

		}

	}