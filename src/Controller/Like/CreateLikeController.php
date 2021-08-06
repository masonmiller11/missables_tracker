<?php
	namespace App\Controller\Like;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Like\LikeDTO;
	use App\DTO\Transformer\RequestTransformer\LikeRequestDTOTransformer;
	use App\Service\ResponseHelper;
	use App\Transformer\LikeEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @Route(path="/like/create", name="like.")
	 */
	class CreateLikeController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 * @param LikeRequestDTOTransformer $dtoTransformer
		 * @param LikeEntityTransformer $entityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request, LikeRequestDTOTransformer $dtoTransformer,
		                       LikeEntityTransformer $entityTransformer): Response {

			$this->createOne($request, $dtoTransformer, LikeDTO::class, $entityTransformer);

			return ResponseHelper::createLikeCreatedResponse();

		}

	}