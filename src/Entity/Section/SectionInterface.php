<?php
	namespace App\Entity\Section;

use App\Entity\EntityInterface;
use App\Entity\Playthrough\Playthrough;
use App\Entity\Playthrough\PlaythroughTemplate;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

interface SectionInterface extends EntityInterface {

	public function getPlaythrough(): PlaythroughTemplate|Playthrough;

	public function getSteps(): Collection|array|Selectable;

	public function getName(): string;

	public function getDescription(): ?string;

	public function getPosition(): int;

	public function getOwner (): User;

	public function setName(string $name) :static;

	public function setDescription(?string $description): static;

	public function setPosition(int $position);


}