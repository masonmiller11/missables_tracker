<?php
	namespace App\Controller;

	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\PlaythroughRepository;
	use App\Request\Payloads\PlaythroughPayload;
	use App\Service\ResponseHelper;
	use App\Transformer\PlaythroughEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;

	/**
	 * @Route(path="/playthroughs/", name="playthroughs.")
	 */
	final class PlaythroughController extends AbstractBaseApiController implements BaseApiControllerInterface {

		/**
		 * PlaythroughController constructor.
		 * @param PlaythroughEntityTransformer $entityTransformer
		 * @param PlaythroughRepository $repository
		 * @param PayloadDecoderRegistryInterface $decoderRegistry
		 */
		public function __construct(
			PlaythroughEntityTransformer $entityTransformer,
			PlaythroughRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry
		) {

			parent::__construct(
				$entityTransformer,
				$repository,
				$decoderRegistry->getDecoder(PlaythroughPayload::class)
			);

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

				$playthrough = $this->doCreate($request, $this->getUser());

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceCreatedResponse('playthroughs/read/' . $playthrough->getId());

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
		 * @Route(path="/{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function list(int $page, int $pageSize, SerializerInterface $serializer): Response {

			$ownerId = $this->getUser()->getId();

			if (!$this->repository instanceof PlaythroughRepository)
				throw new \InvalidArgumentException(
					'repository not instance of type PlaythroughRepository'
				);

			$playthroughs = $this->repository->findAllByOwner($ownerId, $page, $pageSize);

			return ResponseHelper::createReadResponse($playthroughs, $serializer);

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

			$playthrough = $this->repository->find($id);

			return ResponseHelper::createReadResponse($playthrough, $serializer);

		}

		/**
		 * @Route(path="update/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request $request
		 * @param string|int $id
		 *
		 * @return Response
		 */
		public function update(Request $request, string|int $id): Response {

			try {

				$playthrough = $this->doUpdate($request, $id);

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createResourceUpdatedResponse('playthroughs/read/' . $playthrough->getId());

		}

	}