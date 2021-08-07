<?php

	namespace App\DTO\Transformer\RequestTransformer\Step;

	use App\DTO\Section\SectionTemplateDTO;
	use App\DTO\Step\StepDTO;
	use App\DTO\Step\StepTemplateDTO;
	use App\Exception\ValidationException;
	use Symfony\Component\HttpFoundation\Request;

	final class StepRequestTransformer extends AbstractStepRequestTransformer {

		/**
		 * @param Request $request
		 *
		 * @return StepDTO
		 */
		public function transformFromRequest(Request $request): StepDTO {
			$data = json_decode($request->getContent(), true);

			if (!isset($data['section_id'])) {
				throw new ValidationException('step must have a section id');
			}

			$dto = new StepDTO();

			$dto = $this->assembleStepDTO($dto, $data);

			Assert($dto instanceof StepDTO);

			$dto->sectionId = $data['section_id'];

			return $dto;

		}

	}