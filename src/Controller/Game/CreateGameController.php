<?php
	namespace App\Controller\Game;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Game\GameDTO;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\Repository\GameRepository;
	use App\Transformer\GameEntityTransformer;
	use Doctrine\ORM\EntityManager;
	use Doctrine\ORM\Mapping\Entity;
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
		 * @param Request $request
		 * @param GameRequestDTOTransformer $dtoTransformer
		 * @param GameEntityTransformer $gameEntityTransformer
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
		                       GameRequestDTOTransformer $dtoTransformer,
		                       GameEntityTransformer $gameEntityTransformer): Response {

			$game = $this->createOne($request, $dtoTransformer, GameDTO::class, $gameEntityTransformer);

			return $this->responseHelper->createResourceCreatedResponse('games/read/' . $game->getId());

		}

	}