<?php
	namespace App\Controller\PlaythroughTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughTemplateRepository;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @Route(path="/templates/read", name="templates.")
	 */
	final class ReadPlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}",methods={"GET"}, name="read")
		 *
		 * @param string                        $id
		 * @param PlaythroughTemplateRepository $playthroughTemplateRepository
		 *
		 * @return Response
		 */
		public function read(string $id, PlaythroughTemplateRepository $playthroughTemplateRepository): Response {

			$playthroughTemplate = $playthroughTemplateRepository->find($id);

			return $this->responseHelper->createReadResponse($playthroughTemplate);

		}
	}