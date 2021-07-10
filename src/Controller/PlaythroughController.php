<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\PlaythroughResponseDTOTransformer;
	use App\Entity\User;
	use App\Service\ResponseHelper;
	use App\Utility\Responder;
	use Symfony\Component\HttpFoundation\JsonResponse;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Serializer\SerializerInterface;
	/**
	 * Class PlaythroughController
	 *
	 * @package App\Controller
	 * @Route(path="/playthroughs", name="playthroughs.")
	 */
	class PlaythroughController extends AbstractController {

		private PlaythroughResponseDTOTransformer $playthroughResponseDTOTransformer;

		/**
		 * @var ResponseHelper
		 */
		private ResponseHelper $responseHelper;

		public function __construct (PlaythroughResponseDTOTransformer $playthroughResponseDTOTransformer,
		                             ResponseHelper $responseHelper) {
			$this->playthroughResponseDTOTransformer = $playthroughResponseDTOTransformer;
			$this->responseHelper = $responseHelper;
		}

		/**
		 * @Route(path="/{page<\d+>?1}", methods={"GET"}, name="list")
		 *
		 * @param string|int          $page
		 * @param SerializerInterface $serializer
		 *
		 * @return Response
		 */
		public function list(string|int $page, SerializerInterface $serializer): Response {

			$user = $this->getUser();
			assert($user instanceof User);

			$playthroughs = $user->getPlaythroughs();

			return $this->responseHelper->createResponseForMany($playthroughs, $this->playthroughResponseDTOTransformer);

		}



	}