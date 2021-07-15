<?php
	namespace App\Entity\Step;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * Trait PlaythroughStepTrait
	 * @package App\Entity
	 * for use with {@see \App\Entity\PlaythroughStepInterface}
	 */
	trait StepTrait {

		/**
		 * @var string
		 *
		 * @ORM\Column(type="string", length=64)
		 */
		private string $name;

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="text")
		 */
		private ?string $description;

		/**
		 * @var int
		 *
		 * @ORM\Column(type="integer", options={"unsigned":true})
		 */
		private int $position;

		/**
		 * @param string $name
		 * @return static
		 */
		public function setName(string $name): static {
			$this->name = $name;
			return $this;
		}

		/**
		 * @param string|null $description
		 * @return static
		 */
		public function setDescription(?string $description): static {
			$this->description = $description;
			return $this;
		}

		/**
		 * @param int $position
		 * @return static
		 */
		public function setPosition(int $position): static {
			$this->position = $position;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getName(): string {
			return $this->name;
		}

		/**
		 * @return string|null
		 */
		public function getDescription(): ?string {
			return $this->description;
		}

		/**
		 * @return int
		 */
		public function getPosition(): int {
			return $this->position;
		}

	}