<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\PlaythroughTemplateResponseDTOTransformer;
	use App\Entity\User;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Utility\Responder;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;

	/**
	 * Class PlaythroughTemplateController
	 *
	 * @package App\Controller
	 * @Route(path="/templates", name="templates.")
	 */
	class PlaythroughTemplateController extends AbstractController {


		private PlaythroughTemplateResponseDTOTransformer $templateResponseDTOTransformer;
		private PlaythroughTemplateRepository $playthroughTemplateRepository;

		public function __construct (playthroughTemplateResponseDTOTransformer $playthroughTemplateResponseDTOTransformer,
									 PlaythroughTemplateRepository $playthroughTemplateRepository) {

			$this->templateResponseDTOTransformer = $playthroughTemplateResponseDTOTransformer;
			$this->playthroughTemplateRepository = $playthroughTemplateRepository;

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

			return Responder::createResponseFromObject($templates, $this->templateResponseDTOTransformer);

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

			return Responder::createResponseFromObject($templates, $this->templateResponseDTOTransformer);

		}

		/**
		 * @Route(path="/byauthor/{authorID<\d+>}", methods={"GET"}, name="list_by_author")
		 *
		 * @param int $authorID
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function listByAuthor (int $authorID): Response {

			$templates = $this->playthroughTemplateRepository->findByAuthor($authorID);

			return Responder::createResponseFromObject($templates, $this->templateResponseDTOTransformer);

		}

	}