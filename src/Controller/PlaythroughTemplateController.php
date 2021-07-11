<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\PlaythroughTemplateResponseDTOTransformer;
	use App\Entity\EntityInterface;
	use App\Entity\User;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Service\ResponseHelper;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * Class PlaythroughTemplateController
	 *
	 * @package App\Controller
	 * @Route(path="/templates", name="templates.")
	 */
	class PlaythroughTemplateController extends AbstractController {


		/**
		 * @var PlaythroughTemplateResponseDTOTransformer
		 */
		private PlaythroughTemplateResponseDTOTransformer $templateResponseDTOTransformer;

		/**
		 * @var PlaythroughTemplateRepository
		 */
		private PlaythroughTemplateRepository $playthroughTemplateRepository;

		/**
		 * @var ValidatorInterface
		 */
		private ValidatorInterface $validator;

		/**
		 * @var SerializerInterface
		 */
		private SerializerInterface $serializer;

		/**
		 * @var ResponseHelper
		 */
		private ResponseHelper $responseHelper;

		public function __construct (playthroughTemplateResponseDTOTransformer $playthroughTemplateResponseDTOTransformer,
		                             ValidatorInterface $validator,
									 SerializerInterface $serializer,
									 PlaythroughTemplateRepository $playthroughTemplateRepository,
									 ResponseHelper $responseHelper) {

			$this->templateResponseDTOTransformer = $playthroughTemplateResponseDTOTransformer;
			$this->playthroughTemplateRepository = $playthroughTemplateRepository;
			$this->validator = $validator;
			$this->serializer = $serializer;
			$this->responseHelper = $responseHelper;

		}

		/**
		 * @Route(path="/{page<\d+>?1}", methods={"GET"}, name="list_this_users")
		 *
		 * @param string|int          $page
		 *
		 * @return Response
		 */
		public function listThisUsers(string|int $page): Response {

			$user = $this->getUser();
			assert($user instanceof User);

			$templates = $user->getPlaythroughTemplates();

			return $this->responseHelper->createResponseForMany($templates, $this->templateResponseDTOTransformer);

		}

		/**
		 * @Route(path="/bygame/{gameID<\d+>}", methods={"GET"}, name="list_by_game")
		 *
		 * @param int $gameID
		 *
		 * @return Response
		 */
		public function listByGame (int $gameID): Response {

			$templates = $this->playthroughTemplateRepository->findByGame($gameID);

			return $this->responseHelper->createResponseForMany($templates, $this->templateResponseDTOTransformer);
		}

		/**
		 * @Route(path="/byauthor/{authorID<\d+>}", methods={"GET"}, name="list_by_author")
		 *
		 * @param int $authorID
		 *
		 * @return Response
		 */
		public function listByAuthor (int $authorID): Response {

			$templates = $this->playthroughTemplateRepository->findByAuthor($authorID);

			return $this->responseHelper->createResponseForMany($templates, $this->templateResponseDTOTransformer);

		}

	}