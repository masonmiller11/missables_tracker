<?php
	namespace App\Request\Payloads;

	interface PayloadInterface {

		public function doesExist(string $property, bool $useCache):bool;

		public function unset(string $property): void;

	}