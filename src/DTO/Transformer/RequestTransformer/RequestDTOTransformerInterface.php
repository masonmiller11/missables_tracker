<?php
	namespace App\DTO\Transformer\RequestTransformer;

	use Symfony\Component\HttpFoundation\Request;

	interface RequestDTOTransformerInterface {

		public function transformFromRequest(Request $request);
		public function transformFromRequests(Request $requests): iterable;

	}