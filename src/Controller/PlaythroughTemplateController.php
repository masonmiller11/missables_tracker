<?php
	namespace App\Controller;

	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Request\Payloads\PlaythroughTemplatePayload;
	use App\Service\ResponseHelper;
	use App\Transformer\PlaythroughTemplateEntityTransformer;
	use InvalidArgumentException;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;

	/**
	 * @Route(path="/templates/", name="templates.")
	 */
	final class PlaythroughTemplateController extends AbstractBaseApiController implements BaseApiControllerInterface {

		/**
		 * PlaythroughTemplateController constructor.
		 * @param PlaythroughTemplateEntityTransformer $entityTransformer
		 * @param PlaythroughTemplateRepository $repository
		 * @param PayloadDecoderRegistryInterface $decoderRegistry
		 */
		public function __construct(
			PlaythroughTemplateEntityTransformer $entityTransformer,
			PlaythroughTemplateRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry
		) {

			parent::__construct(
				$entityTransformer,
				$repository,
				$decoderRegistry->getDecoder(PlaythroughTemplatePayload::class));
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

				$playthroughTemplate = $this->doCreate($request, $this->getUser());

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceCreatedResponse('templates/read/' . $playthroughTemplate->getId(), $playthroughTemplate->getId());

		}

		/**
		 * @Route(path="delete/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int $id
		 *
		 * @return Response
		 */
		public function delete(string|int $id): Response {

			$this->doDelete($id);

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

			if (!$this->repository instanceof PlaythroughTemplateRepository)
				throw new \InvalidArgumentException(
					'repository not instance of type PlaythroughTemplateRepository'
				);

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

			try {

				$playthroughTemplate = $this->doUpdate($request, $id);

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceUpdatedResponse('playthroughs/read/' . $playthroughTemplate->getId());

		}

		/**
		 * @Route(path="{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list_this_users")
		 *
		 * @param int $page
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function listThisUsers(int $page, int $pageSize, SerializerInterface $serializer): Response {

			$ownerId = $this->getUser()->getId();

			if (!$this->repository instanceof PlaythroughTemplateRepository)
				throw new \InvalidArgumentException(
					'repository not instance of type PlaythroughTemplateRepository'
				);

			$templates = $this->repository->findAllByAuthor($ownerId, $page, $pageSize);

			return ResponseHelper::createReadResponse($templates, $serializer, true);

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
				throw new InvalidArgumentException(
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
				throw new InvalidArgumentException(
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
				throw new InvalidArgumentException(
					'repository not instance of type PlaythroughTemplateRepository'
				);
			$templates = $this->repository->findAllOrderByLikes($page, $pageSize);

			return ResponseHelper::createReadResponse($templates, $serializer);

		}
	}