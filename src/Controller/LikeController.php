<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\LikeRequestDTOTransformer;
	use App\Exception\PayloadDecoderException;
	use App\Exception\ValidationException;
	use App\Payload\Registry\PayloadDecoderRegistryInterface;
	use App\Repository\LikeRepository;
	use App\Request\Payloads\LikePayload;
	use App\Service\ResponseHelper;
	use App\Transformer\LikeEntityTransformer;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @Route(path="/like/", name="like.")
	 */
	class LikeController extends AbstractBaseApiController {

		/**
		 * LikeController constructor.
		 * @param ValidatorInterface $validator
		 * @param LikeEntityTransformer $entityTransformer
		 * @param LikeRequestDTOTransformer $DTOTransformer
		 * @param LikeRepository $repository
		 * @param PayloadDecoderRegistryInterface $decoderRegistry
		 */
		public function __construct(
			ValidatorInterface $validator,
			LikeEntityTransformer $entityTransformer,
			LikeRequestDTOTransformer $DTOTransformer,
			LikeRepository $repository,
			PayloadDecoderRegistryInterface $decoderRegistry
		) {

			parent::__construct($validator,
				$entityTransformer,
				$DTOTransformer,
				$repository,
				$decoderRegistry->getDecoder(LikePayload::class)
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

				$this->doCreate($request, $this->getUser());

			} catch (PayloadDecoderException | ValidationException $exception) {

				return $this->handleApiException($request, $exception);

			}

			return ResponseHelper::createLikeCreatedResponse();

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
		 * @Route(path="{page<\d+>?1}/{pageSize<\d+>?20}", methods={"GET"}, name="list")
		 *
		 * @param int $page
		 * @param int $pageSize
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function list(int $page, int $pageSize, SerializerInterface $serializer): Response {

			$ownerId = $this->getUser()->getId();

			if (!$this->repository instanceof LikeRepository)
				throw new \InvalidArgumentException('repository not instance of type LikeRepository');

			$playthroughs = $this->repository->findAllByOwner($ownerId, $page, $pageSize);

			return ResponseHelper::createReadResponse($playthroughs, $serializer);

		}

		protected function update(Request $request, int $id): Response {
			// TODO: Implement update() method.
		}

		protected function read(int $id, SerializerInterface $serializer): Response {
			// TODO: Implement read() method.
		}

	}