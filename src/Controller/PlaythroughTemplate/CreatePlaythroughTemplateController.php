<?php
	namespace App\Controller\PlaythroughTemplate;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\PlaythroughTemplateRequestDTOTransformer;
	use App\Repository\GameRepository;
	use App\Service\EntityAssembler;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\PlaythroughTemplate
	 * @Route(path="/templates/create", name="templates.")
	 */
	class CreatePlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request                                  $request
		 * @param PlaythroughTemplateRequestDTOTransformer $transformer
		 * @param GameRepository                           $gameRepository
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
							   PlaythroughTemplateRequestDTOTransformer $transformer,
							   GameRepository $gameRepository): Response {

			$dto = $this->transformOne($request, $transformer);

			Assert($dto instanceof PlaythroughTemplateDTO);
			$this->validate($dto);

			$user = $this->getUser();
			$game = $gameRepository->find($dto->gameID);

			if (!$game) {
				throw new NotFoundHttpException();
			}

			$playthroughTemplate = EntityAssembler::assembePlaythroughTemplate($dto, $game, $user);
			$this->entityManager->persist($playthroughTemplate);
			$this->entityManager->flush();

			return $this->responseHelper->returnResourceCreatedResponse('templates/read/' . $playthroughTemplate->getId());

		}
	}