<?php
	namespace App\Entity;

	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;

	interface PlaythroughInterface {

		public function getSteps(): Collection|array|Selectable;

		public function getOwner(): User;

		public function getGame(): Game;

	}