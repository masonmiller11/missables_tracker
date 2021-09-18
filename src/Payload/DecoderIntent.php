<?php
	namespace App\Payload;

	use App\Utility\ConstantsClassTrait;

	final class DecoderIntent {

		use ConstantsClassTrait;

		public const CREATE = 'create';
		public const UPDATE = 'update';
		public const CLONE = 'clone';

	}