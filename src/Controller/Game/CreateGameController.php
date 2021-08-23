<?php
	namespace App\Controller\Game;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Game\GameDTO;
	use App\DTO\Transformer\RequestTransformer\GameRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\RequestDTOTransformerInterface;
	use App\Repository\GameRepository;
	use App\Service\IGDBHelper;
	use App\Service\ResponseHelper;
	use App\Transformer\EntityTransformerInterface;
	use App\Transformer\GameEntityTransformer;
	use Doctrine\ORM\EntityManager;
	use Doctrine\ORM\EntityManagerInterface;
	use Doctrine\ORM\Mapping\Entity;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\RequestStack;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\Validator\Exception\ValidationFailedException;
	use Symfony\Component\Validator\Validator\ValidatorInterface;

	/**
	 * Class GameController
	 *
	 * @package App\Controller
	 * @Route(path="/games/create", name="games.")
	 */
	final class CreateGameController extends AbstractBaseApiController {

		#[Pure]
		public function __construct(
			IGDBHelper $IGDBHelper,
			ResponseHelper $responseHelper,
			RequestStack $request,
			EntityManagerInterface $entityManager,
			ValidatorInterface $validator,
			GameEntityTransformer $entityTransformer,
			GameRequestDTOTransformer $DTOTransformer,
			GameRepository $repository
		) {
			parent::__construct(
				$IGDBHelper,
				$responseHelper,
				$request,
				$entityManager,
				$validator,
				$entityTransformer,
				$DTOTransformer,
				$repository
			);
		}

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request $request
		 *
		 * @return Response
		 */
		public function create(Request $request): Response {
			try {

				$game = $this->createOne($request);

			} catch (ValidationFailedException $exception) {

				$errors = [];

				foreach ($exception->getViolations() as $error) {
					$errors[] = $error->getMessage();

					return ResponseHelper::createValidationErrorResponse($errors);
				}
			}

			return $this->responseHelper->createResourceCreatedResponse('games/read/' . $game->getId());

		}

	}