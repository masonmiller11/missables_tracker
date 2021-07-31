<?php

	namespace App\DTO\Transformer\RequestTransformer\Section;

	use App\DTO\Section\SectionDTO;
	use App\Exception\ValidationException;
	use Symfony\Component\HttpFoundation\Request;

	class SectionRequestTransformer extends AbstractSectionRequestTransformer {

		/**
		 * @param Request $request
		 *
		 * @return SectionDTO
		 */
		public function transformFromRequest(Request $request): SectionDTO {

			$data = json_decode($request->getContent(), true);

			if (!isset($data['playthrough_id'])) {
				throw new ValidationException('sections must have a playthrough id');
			}

			$dto = new SectionDTO();

			$dto = $this->assembleSectionDTO($dto, $data);

			Assert($dto instanceof SectionDTO);

			$dto->playthroughId = $data['playthrough_id'];

			return $dto;

		}

	}