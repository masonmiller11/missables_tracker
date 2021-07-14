<?php
	namespace App\Entity\Step;

	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\User;

	interface PlaythroughStepInterface extends EntityInterface {

		public function getPlaythrough(): PlaythroughTemplate|Playthrough;

		public function getOwner(): User;

		public function getName(): string;

		public function getDescription(): ?string;

		public function setName(string $name) :static;

		public function setDescription(?string $description): static;

	}