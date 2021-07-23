<?php
	namespace App;

	use App\Utility\ConstantsClassTrait;

	final class Genre {

		use ConstantsClassTrait;

		public const ACTION = 'action';
		public const ARPG = 'arpg';
		public const ACTION_ADVENTURE = 'action adventure';
		public const ADVENTURE = 'adventure';
		public const JRPG = 'jrpg';
		public const ROLE_PLAYING = 'role playing';
		public const OPEN_WORLD = 'open world';
		public const SIMULATION = 'simulation';
		public const STEALTH = 'stealth';
		public const VISUAL_NOVEL = 'visual novel';
		public const NOT_AVAILABLE = 'No genre available';

	}