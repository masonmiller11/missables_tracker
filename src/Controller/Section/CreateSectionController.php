<?php
	namespace App\Controller\Section;

	use App\Controller\AbstractBaseApiController;
	use App\DTO\Playthrough\PlaythroughDTO;
	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\DTO\Section\SectionDTO;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\Playthrough\PlaythroughTemplateRequestDTOTransformer;
	use App\DTO\Transformer\RequestTransformer\Section\SectionRequestTransformer;
	use App\Repository\GameRepository;
	use App\Transformer\PlaythroughEntityTransformer;
	use App\Transformer\SectionEntityTransformer;
	use Symfony\Component\HttpFoundation\Request;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Annotation\Route;

	/**
	 * @package App\Controller
	 * @Route(path="/section/create", name="section.")
	 */
	final class CreateSectionController extends AbstractBaseApiController {

		/**
		 * @Route(methods={"POST"}, name="create")
		 *
		 * @param Request                   $request
		 * @param SectionRequestTransformer $transformer
		 * @param SectionEntityTransformer  $sectionEntityTransformer
		 *
		 * @return Response
		 * @throws \Exception
		 */
		public function create(Request $request,
			SectionRequestTransformer $transformer,
			SectionEntityTransformer $sectionEntityTransformer): Response {

			$section = $this->createOne($request,
				$transformer,
				SectionDTO::class,
				$sectionEntityTransformer
			);

			return $this->responseHelper->createResourceCreatedResponse('section/read/' . $section->getId());

		}

	}