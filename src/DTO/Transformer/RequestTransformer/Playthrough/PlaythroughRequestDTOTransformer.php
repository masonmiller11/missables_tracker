<?php
	namespace App\DTO\Transformer\RequestTransformer\Playthrough;

	use App\DTO\Playthrough\PlaythroughDTO;
	use Symfony\Component\HttpFoundation\Request;

	class PlaythroughRequestDTOTransformer extends AbstractPlaythroughRequestTransformer {

		public function transformFromRequest(Request $request): PlaythroughDTO {

			$data = json_decode($request->getContent(), true);

			if (!isset($data['game'])) {
				throw new \OutOfBoundsException('playthroughs must include game key');
			}

			if (!isset($data['template_id'])) {
				throw new \OutOfBoundsException('playthroughs must include template_id key');
			}

			$dto = new PlaythroughDTO();

			$dto = $this->assemblePlaythroughDTO($dto, $data);

			Assert($dto instanceof PlaythroughDTO);

			$dto->templateId = $data['template_id'];

			return $dto;

		}

	}