<?php
	namespace App\Controller\Playthrough;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughRepository;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @Route(path="/playthroughs/read", name="playthroughs.")
	 */
	class ReadPlaythroughController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}",methods={"GET"}, name="read")
		 *
		 * @param string $id
		 * @param PlaythroughRepository $playthroughRepository
		 *
		 * @return Response
		 */
		public function read(string $id, PlaythroughRepository $playthroughRepository): Response {

			$playthrough = $playthroughRepository->find($id);

			return $this->responseHelper->createReadResponse($playthrough);

		}

	}