<?php

	namespace App\DTO\Transformer\RequestTransformer\Section;

	use App\DTO\Section\SectionTemplateDTO;
	use App\Exception\ValidationException;
	use Symfony\Component\HttpFoundation\Request;

	final class SectionTemplateRequestTransformer extends AbstractSectionRequestTransformer {

		/**
		 * @param Request $request
		 *
		 * @return SectionTemplateDTO
		 */
		public function transformFromRequest(Request $request): SectionTemplateDTO {

			$data = json_decode($request->getContent(), true);

			if (!isset($data['template_id'])) {
				throw new ValidationException('section templates must have a template id');
			}

			$dto = new SectionTemplateDTO();

			$dto = $this->assembleSectionDTO($dto, $data);

			Assert($dto instanceof SectionTemplateDTO);

			$dto->templateId = $data['template_id'];

			return $dto;

		}

	}