<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Service\ResponseHelper;
	use App\Transformer\PlaythroughTemplateEntityTransformer;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @Route(path="/templates/", name="templates.")
	 */
	final class PlaythroughTemplateController extends AbstractBaseApiController {

		#[Pure]
		public function __construct(
			ValidatorInterface $validator, PlaythroughTemplateEntityTransformer $entityTransformer,
			PlaythroughTemplateRequestDTOTransformer $DTOTransformer,
			PlaythroughTemplateRepository $repository
		) {

			parent::__construct($validator, $entityTransformer, $DTOTransformer, $repository);

		}

		/**
		 * @Route(path="create", methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function create(Request $request): Response {

			try {

				$playthroughTemplate = $this->createOne($request);

			} catch (ValidationFailedException $exception) {

				return ResponseHelper::createValidationErrorResponse($exception);

			}

			return ResponseHelper::createResourceCreatedResponse('templates/read/' . $playthroughTemplate->getId());

		}

		/**
		 * @Route(path="delete/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int $id
		 *
		 * @return Response
		 */
		public function delete(string|int $id): Response {

			$this->deleteOne($id);

			return ResponseHelper::createResourceDeletedResponse();

		}

		/**
		 * @Route(path="read/{id<\d+>}",methods={"GET"}, name="read")
		 *
		 * @param int $id
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function read(int $id, SerializerInterface $serializer): Response {

			$playthroughTemplate = $this->repository->find($id);

			return ResponseHelper::createReadResponse($playthroughTemplate, $serializer);

		}

		/**
		 * @Route(path="update/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request $request
		 * @param int $id
		 *
		 * @return Response
		 */
		public function update(Request $request, int $id): Response {

			$playthroughTemplate = $this->updateOne($request, $id);

			return ResponseHelper::createResourceUpdatedResponse('templates/read/' . $playthroughTemplate->getId());

		}

		/**
		 * @Route(path="{page<\d+>?1}", methods={"GET"}, name="list_this_users")
		 *
		 * @param int $page
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function listThisUsers(int $page, SerializerInterface $serializer): Response {

			//TODO use paginator for this in future.

			$user = $this->getUser();
			$templates = $user->getPlaythroughTemplates();

			return ResponseHelper::createReadResponse($templates, $serializer);

		}

		/**
		 * @Route(path="bygame/{gameID<\d+>}/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list_by_game")
		 *
		 * @param int $gameID
		 * @param int $page
		 * @param int $pageSize
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function listByGame(int $gameID, int $page, int $pageSize, SerializerInterface $serializer): Response {

			if (!$this->repository instanceof PlaythroughTemplateRepository)
				throw new \InvalidArgumentException(
					'repository not instance of type PlaythroughTemplateRepository'
				);

			$templates = $this->repository->findAllByGame($gameID, $page, $pageSize);

			return ResponseHelper::createReadResponse($templates, $serializer);

		}

		/**
		 * @Route(path="byauthor/{authorID<\d+>}/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"},
		 *                                                                          name="list_by_author")
		 *
		 * @param int $authorID
		 * @param int $page
		 * @param int $pageSize
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function listByAuthor(int $authorID, int $page, int $pageSize, SerializerInterface $serializer
		): Response {

			if (!$this->repository instanceof PlaythroughTemplateRepository)
				throw new \InvalidArgumentException(
					'repository not instance of type PlaythroughTemplateRepository'
				);

			$templates = $this->repository->findAllByAuthor($authorID, $page, $pageSize);

			return ResponseHelper::createReadResponse($templates, $serializer);

		}

		/**
		 * @Route(path="all/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list_all")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function list(int $page, int $pageSize, SerializerInterface $serializer): Response {

			if (!$this->repository instanceof PlaythroughTemplateRepository)
				throw new \InvalidArgumentException(
					'repository not instance of type PlaythroughTemplateRepository'
				);
			$templates = $this->repository->findAllOrderByLikes($page, $pageSize);

			return ResponseHelper::createReadResponse($templates, $serializer);

		}
	}