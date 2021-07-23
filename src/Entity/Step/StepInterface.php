<?php
	namespace App\Entity\Step;

	use App\Entity\EntityInterface;
	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Section\Section;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\User;

	interface StepInterface extends EntityInterface {

		public function getSection(): Section | SectionTemplate;

		public function getName(): string;

		public function getDescription(): ?string;

		public function getPosition(): int;

		public function setName(string $name) :static;

		public function setDescription(?string $description): static;

		public function setPosition(int $position);

	}