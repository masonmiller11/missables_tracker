<?php

	namespace App\DTO\Transformer\RequestTransformer\Step;

	use App\DTO\Section\SectionTemplateDTO;
	use App\DTO\Step\StepTemplateDTO;
	use App\Exception\ValidationException;
	use Symfony\Component\HttpFoundation\Request;

	final class StepTemplateRequestTransformer extends AbstractStepRequestTransformer {

		/**
		 * @param Request $request
		 *
		 * @return StepTemplateDTO
		 */
		public function transformFromRequest(Request $request): StepTemplateDTO {
			$data = json_decode($request->getContent(), true);

			if (!isset($data['section_template_id'])) {
				throw new ValidationException('step templates must have a section template id');
			}

			$dto = new StepTemplateDTO();

			$dto = $this->assembleStepDTO($dto, $data);

			Assert($dto instanceof StepTemplateDTO);

			$dto->sectionTemplateId = $data['section_template_id'];

			return $dto;

		}

	}