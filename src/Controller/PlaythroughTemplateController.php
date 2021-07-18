<?php
	namespace App\Controller;

	use App\DTO\Transformer\ResponseTransformer\PlaythroughTemplateResponseDTOTransformer;
	use App\Repository\PlaythroughTemplateRepository;
	use Symfony\Component\Routing\Annotation\Route;
	use Symfony\Component\HttpFoundation\Response;

	/**
	 * Class PlaythroughTemplateController
	 *
	 * @package App\Controller
	 * @Route(path="/templates", name="templates.")
	 */
	final class PlaythroughTemplateController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{page<\d+>?1}", methods={"GET"}, name="list_this_users")
		 *
		 * @param string|int $page
		 * @param PlaythroughTemplateResponseDTOTransformer $transformer
		 * @return Response
		 */
		public function listThisUsers(string|int $page, PlaythroughTemplateResponseDTOTransformer $transformer): Response {

			try {
				$user = $this->getUser();

				$templates = $user->getPlaythroughTemplates();

				$dtos = $this->transformMany($templates, $transformer);

				return $this->responseHelper->createResponseForMany($dtos);
			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

		/**
		 * @Route(path="/bygame/{gameID<\d+>}", methods={"GET"}, name="list_by_game")
		 *
		 * @param int $gameID
		 * @param PlaythroughTemplateRepository $repository
		 * @param PlaythroughTemplateResponseDTOTransformer $transformer
		 * @return Response
		 */
		public function listByGame (int $gameID, PlaythroughTemplateRepository $repository,
		                            PlaythroughTemplateResponseDTOTransformer $transformer ): Response {

			try {
				$templates = $repository->findByGame($gameID);

				$dtos = $this->transformMany($templates, $transformer);

				return $this->responseHelper->createResponseForMany($dtos);
			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

		/**
		 * @Route(path="/byauthor/{authorID<\d+>}", methods={"GET"}, name="list_by_author")
		 *
		 * @param int $authorID
		 * @param PlaythroughTemplateRepository $repository
		 * @param PlaythroughTemplateResponseDTOTransformer $transformer
		 * @return Response
		 */
		public function listByAuthor (int $authorID, PlaythroughTemplateRepository $repository,
		                              PlaythroughTemplateResponseDTOTransformer $transformer): Response {

			try {
				$templates = $repository->findByAuthor($authorID);

				$dtos = $this->transformMany($templates, $transformer);

				return $this->responseHelper->createResponseForMany($dtos);
			} catch (\Exception $e) {

				return $this->responseHelper->createErrorResponse($e);

			}

		}

	}