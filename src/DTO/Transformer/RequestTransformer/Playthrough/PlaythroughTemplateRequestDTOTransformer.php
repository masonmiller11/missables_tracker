<?php
	namespace App\DTO\Transformer\RequestTransformer\Playthrough;

	use App\DTO\Playthrough\PlaythroughTemplateDTO;
	use App\Exception\ValidationException;
	use Symfony\Component\HttpFoundation\Request;

	class PlaythroughTemplateRequestDTOTransformer extends AbstractPlaythroughRequestTransformer {

		public function transformFromRequest(Request $request): PlaythroughTemplateDTO {

			$data = json_decode($request->getContent(), true);

			if (!isset($data['game'])) {
				throw new \OutOfBoundsException('playthrough templates must include game key');
			}

			$dto = new PlaythroughTemplateDTO();

			$dto = $this->assemblePlaythroughDTO($dto, $data);

			Assert($dto instanceof PlaythroughTemplateDTO);

			return $dto;

		}

	}