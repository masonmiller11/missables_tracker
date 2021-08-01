<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\User;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class PlaythroughTemplateEntityTransformer extends AbstractPlaythroughEntityTransformer {

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
		 * @param PlaythroughTemplateDTO $dto
		 * @param User $user
		 *
		 * @return PlaythroughTemplate
		 */
		public function assemble (PlaythroughTemplateDTO $dto, User $user): PlaythroughTemplate {

			$this->user = $user;

			return $this->create($dto);

		}

		/**
		 *
		 * @param DTOInterface $dto
		 * @param bool $skipValidation
		 *
		 * @return PlaythroughTemplate
		 */
		public function create (DTOInterface $dto, bool $skipValidation = false): PlaythroughTemplate {

			if (!$skipValidation) {
				$this->validate($dto);
			}

			assert($dto instanceof PlaythroughTemplateDTO);

			$game = $this->gameRepository->find($dto->gameID);

			if (!$game) {
				throw new NotFoundHttpException('game not found');
			}

			$playthroughTemplate = new PlaythroughTemplate($dto->name, $dto->description, $this->user, $game, $dto->visibility);

			$this->entityManager->persist($playthroughTemplate);
			$this->entityManager->flush();

			return $playthroughTemplate;

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return EntityInterface
		 */
		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface {

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);
			$playthroughTemplate = $this->repository->find($id);

			$tempDTO->gameID = $playthroughTemplate->getGame()->getId();
			$this->validate($tempDTO);

			return $this->doUpdate(json_decode($request->getContent(), true), $playthroughTemplate);

		}

	}