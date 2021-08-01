<?php
	namespace App\Controller\Step;

	use App\Controller\AbstractBaseApiController;
	use App\Repository\PlaythroughTemplateRepository;
	use App\Repository\SectionRepository;
	use App\Repository\StepRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use App\Transformer\SectionEntityTransformer;
	use App\Transformer\StepEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * Class CreatePlaythroughTemplateController
	 *
	 * @package App\Controller\
	 * @Route(path="/step/delete", name="step.")
	 */
	final class DeleteStepController extends AbstractBaseApiController {

		/**
		 * @Route(path="/{id<\d+>}", methods={"DELETE"}, name="delete")
		 *
		 * @param string|int            $id
		 * @param StepRepository        $stepRepository
		 * @param StepEntityTransformer $stepEntityTransformer
		 *
		 * @return Response
		 */
		public function delete(string|int $id,
			StepRepository $stepRepository,
			StepEntityTransformer $stepEntityTransformer): Response {

			$this->doDelete($id, $stepEntityTransformer, $stepRepository);

			return $this->responseHelper->createResourceDeletedResponse();

		}
	}