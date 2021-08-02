<?php
	namespace App\Controller\Playthrough;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class ListPlaythroughController
	 *
	 * @package App\Controller
	 * @Route(path="/playthroughs/update", name="playthroughs.")
	 */
	class UpdatePlaythroughController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"PATCH"}, name="update")
		 *
		 * @param Request                      $request
		 * @param string|int                   $id
		 * @param PlaythroughRepository        $playthroughRepository
		 * @param PlaythroughEntityTransformer $playthroughEntityTransformer
		 *
		 * @return Response
		 */
		public function update(Request $request, string|int $id,
			PlaythroughRepository $playthroughRepository,
			PlaythroughEntityTransformer $playthroughEntityTransformer): Response {

			$playthroughTemplate = $this->updateOne($request,
				$id,
				$playthroughEntityTransformer,
				$playthroughRepository);

			return $this->responseHelper->createResourceUpdatedResponse('playthroughs/read/' . $playthroughTemplate->getId());

		}

	}