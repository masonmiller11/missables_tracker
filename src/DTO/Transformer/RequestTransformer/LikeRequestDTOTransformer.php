<?php
	namespace App\DTO\Transformer\RequestTransformer;

	use App\DTO\Like\LikeDTO;
	use App\Exception\ValidationException;
	use Symfony\Component\HttpFoundation\Request;

	class LikeRequestDTOTransformer extends AbstractRequestDTOTransformer {

		public function transformFromRequest(Request $request): LikeDTO {

			$data = json_decode($request->getContent(), true);

			$dto = new LikeDTO();

			if (!isset($data['template_id'])) {
				throw new ValidationException('likes must include template id');
			}

			$dto->templateID = $data['template_id'];

			return $dto;

		}

	}