<?php
	namespace App\Utility;

	use App\Utility\ConstantsClassTrait;

	final class InternetGameDatabaseEndpoints {

		use ConstantsClassTrait;

		public const DOMAIN = 'https://api.igdb.com/v4';
		public const TOKEN = 'https://id.twitch.tv/oauth2/token';
		public const GAMES = self::DOMAIN . '/games';

	}