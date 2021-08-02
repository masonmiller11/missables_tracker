<?php
	namespace App\Controller\Playthrough;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Playthrough\PlaythroughDTO;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\Repository\GameRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class ListPlaythroughController
	 *
	 * @package App\Controller
	 * @Route(path="/playthroughs/create", name="playthroughs.")
	 */
	class CreatePlaythroughController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 * @param PlaythroughRequestDTOTransformer $transformer
		 * @param PlaythroughEntityTransformer $playthroughTemplateEntityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
		                       PlaythroughRequestDTOTransformer $transformer,
		                       PlaythroughEntityTransformer $playthroughTemplateEntityTransformer): Response {

			$playthrough = $this->createOne($request,
				$transformer,
				PlaythroughDTO::class,
				$playthroughTemplateEntityTransformer
			);

			return $this->responseHelper->createResourceCreatedResponse('playthroughs/read/' . $playthrough->getId());

		}

	}