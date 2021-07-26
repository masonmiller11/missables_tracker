<?php
	namespace App\DTO\Transformer\RequestTransformer;

	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\Exception\ValidationException;
	use http\Exception\InvalidArgumentException;
	use Symfony\Component\HttpFoundation\Request;

	class PlaythroughTemplateRequestDTOTransformer extends AbstractRequestDTOTransformer {

		public function transformFromRequest(Request $request): PlaythroughTemplateDTO {

			$data = json_decode($request->getContent(), true);

			if (!isset($data['game'])) {
				throw new ValidationException('playthrough templates must include game key');
			}

			$dto = new PlaythroughTemplateDTO();

			$dto->visibility = $data['visibility'] ?? false;
			$dto->gameID = $data['game'];
			$dto->sections = $data['sections'] ?? [];
			$dto->sectionPositions = $data['section_positions'] ?? [];
			$dto->stepPositions = $data['step_positions'] ?? [];
			$dto->name = $data['name'] ?? 'untitled';
			$dto->description = $data['description'] ?? 'no description';

			return $dto;

		}

	}