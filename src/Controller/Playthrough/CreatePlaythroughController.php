<?php
	namespace App\Controller\Playthrough;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Playthrough\PlaythroughDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughRequestDTOTransformer;
	use App\Transformer\PlaythroughEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @Route(path="/playthroughs/create", name="playthroughs.")
	 */
	class CreatePlaythroughController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 * @param PlaythroughRequestDTOTransformer $dtoTransformer
		 * @param PlaythroughEntityTransformer $entityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
		                       PlaythroughRequestDTOTransformer $dtoTransformer,
		                       PlaythroughEntityTransformer $entityTransformer): Response {

			$playthrough = $this->createOne($request,
				$dtoTransformer,
				PlaythroughDTO::class,
				$entityTransformer
			);

			return $this->responseHelper->createResourceCreatedResponse('playthroughs/read/' . $playthrough->getId());

		}

	}