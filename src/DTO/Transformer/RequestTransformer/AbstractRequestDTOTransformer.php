<?php
	namespace App\DTO\Transformer\RequestTransformer;

	use Symfony\Component\HttpFoundation\Request;

	abstract class AbstractRequestDTOTransformer implements RequestDTOTransformerInterface {

		public function transformFromRequests(Request $requests): iterable {
			$dto = [];

			foreach ($requests as $request) {
				$dto[] = $this->transformFromRequest($request);
			}

			return $dto;
		}

	}