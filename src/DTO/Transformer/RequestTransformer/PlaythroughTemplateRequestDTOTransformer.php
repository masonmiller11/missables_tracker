<?php
	namespace App\DTO\Transformer\RequestTransformer;

	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use Symfony\Component\HttpFoundation\Request;

	class PlaythroughTemplateRequestDTOTransformer extends AbstractRequestDTOTransformer {

		public function transformFromRequest(Request $request): PlaythroughTemplateDTO {

			$data = json_decode($request->getContent(), true);

			$dto = new PlaythroughTemplateDTO();

			$dto->visibility = $data['visibility'];
			$dto->ownerID = $data['owner'];
			$dto->gameID = $data['game'];
			$dto->sections = $data['sections'];
			$dto->sectionPositions = $data['section_positions'];
			$dto->stepPositions = $data['step_positions'];
			$dto->name = $data['name'];
			$dto->description = $data['description'];

			return $dto;

		}

	}