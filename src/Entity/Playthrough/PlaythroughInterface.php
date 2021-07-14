<?php
	namespace App\Entity\Playthrough;

	use App\Entity\Game;
	use App\Entity\User;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use App\Entity\EntityInterface;

	interface PlaythroughInterface extends EntityInterface {

		public function getSections(): Collection|array|Selectable;

		public function getOwner(): User;

		public function getGame(): Game;

		public function isVisible(): bool;

		public function setVisibility(bool $visibility): static;

		public function getTitle(): string;

		public function setTitle (string $title): static;

		public function getDescription(): string;

		public function setDescription(string $description): static;



	}