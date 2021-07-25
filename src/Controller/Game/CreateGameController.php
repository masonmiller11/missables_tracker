<?php
	namespace App\Controller\Game;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Game\GameDTO;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\Service\EntityAssembler;
	use Doctrine\ORM\EntityManager;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class GameController
	 *
	 * @package App\Controller
	 * @Route(path="/games/create", name="games.")
	 */
	final class CreateGameController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request                   $request
		 * @param GameRequestDTOTransformer $transformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request, GameRequestDTOTransformer $transformer): Response {

			$dto = $this->transformOne($request, $transformer);

			Assert($dto instanceof GameDTO);
			$this->validate($dto);

			$game = EntityAssembler::assembleGame($dto);
			$this->entityManager->persist($game);
			$this->entityManager->flush();

			return $this->responseHelper->returnResourceCreatedResponse('games/read/' . $game->getId());

		}

	}