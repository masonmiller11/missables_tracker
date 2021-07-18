<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\PlaythroughResponseDTOTransformer;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\HttpFoundation\Response;
	/**
	 * Class PlaythroughController
	 *
	 * @package App\Controller
	 * @Route(path="/playthroughs", name="playthroughs.")
	 */
	final class PlaythroughController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{page<\d+>?1}", methods={"GET"}, name="list")
		 *
		 * @param string|int $page
		 * @param PlaythroughResponseDTOTransformer $transformer
		 * @return Response
		 * @throws \Exception
		 */
		public function list(string|int $page, PlaythroughResponseDTOTransformer $transformer): Response {

			$user = $this->getUser();

			try {

				$playthroughs = $user->getPlaythroughs();

				$dtos = $this->transformMany($playthroughs, $transformer);

				return $this->responseHelper->createResponseForMany($dtos);

			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

	}