<?php
	namespace App\Controller;

	use App\DTO\Transformer\RequestTransformer\LikeRequestDTOTransformer;
	use App\Repository\LikeRepository;
	use App\Service\ResponseHelper;
	use App\Transformer\LikeEntityTransformer;
	use Doctrine\ORM\EntityManagerInterface;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * @Route(path="/like/", name="like.")
	 */
	class LikeController extends AbstractBaseApiController {

		public function __construct(RequestStack $request, EntityManagerInterface $entityManager,
		                            ValidatorInterface $validator, LikeEntityTransformer $entityTransformer,
		                            LikeRequestDTOTransformer $DTOTransformer,
		                            LikeRepository $repository) {

			parent::__construct($request, $entityManager, $validator, $entityTransformer, $DTOTransformer, $repository);

		}

		/**
		 * @Route(path="create", methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 * @return Response
		 */
		public function create(Request $request): Response {

			$this->createOne($request);

			return ResponseHelper::createLikeCreatedResponse();

		}

		/**
		 * @Route(path="delete/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int $id
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
		 * @return Response
		 */
		public function list(int $page, int $pageSize, SerializerInterface $serializer): Response {

			$ownerId = $this->getUser()->getId();

			if (!$this->repository instanceof LikeRepository) throw new \InvalidArgumentException('repository not instance of type LikeRepository');
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