<?php
	namespace App\Transformer;

	use App\DTO\DTOInterface;
	use App\DTO\Playthrough\PlaythroughDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughRequestDTOTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\Playthrough;
	use App\Entity\User;
	use App\Repository\GameRepository;
	use App\Repository\PlaythroughRepository;
	use App\Repository\PlaythroughTemplateRepository;
	use Doctrine\ORM\EntityManagerInterface;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	final class PlaythroughEntityTransformer extends AbstractPlaythroughEntityTransformer {

		/**
		 * @var GameRepository
		 */
		private GameRepository $gameRepository;

		/**
		 * @var User
		 */
		private User $user;

		/**
		 * @var PlaythroughRequestDTOTransformer
		 */
		private PlaythroughRequestDTOTransformer $DTOTransformer;

		private PlaythroughRepository $playthroughRepository;

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
			$this->playthroughRepository = $playthroughRepository;
			$this->repository = $playthroughRepository;

		}

		/**
		 * @param PlaythroughDTO $dto
		 * @param User $user
		 *
		 * @return Playthrough
		 */
		public function assemble (PlaythroughDTO $dto, User $user): Playthrough {

			$this->user = $user;

			return $this->create($dto);

		}

		/**
		 *
		 * @param DTOInterface $dto
		 * @param bool $skipValidation
		 *
		 * @return Playthrough
		 */
		public function create (DTOInterface $dto, bool $skipValidation = false): Playthrough {

			if (!$skipValidation) {
				$this->validate($dto);
			}

			assert($dto instanceof PlaythroughDTO);

			$game = $this->gameRepository->find($dto->gameID);

			if (!$game) {
				throw new NotFoundHttpException('game not found');
			}

			$template = $this->playthroughTemplateRepository->find($dto->templateId);

			if (!$template) {
				throw new NotFoundHttpException('template not found');
			}

			$playthrough = new Playthrough($dto->name, $dto->description, $game, $dto->templateId, $this->user, $dto->visibility);

			$this->entityManager->persist($playthrough);
			$this->entityManager->flush();

			return $playthrough;

		}

		/**
		 * @param int $id
		 * @param Request $request
		 * @param bool $skipValidation
		 * @return EntityInterface
		 */
		public function update(int $id, Request $request, bool $skipValidation = false): EntityInterface {

			$tempDTO = $this->DTOTransformer->transformFromRequest($request);

			$playthrough = $this->playthroughRepository->find($id);

			$tempDTO->gameID = $playthrough->getGame()->getId();
			$tempDTO->templateId = $playthrough->getTemplateId();
			$this->validate($tempDTO);

			return $this->doUpdate(json_decode($request->getContent(), true), $playthrough);

		}

	}