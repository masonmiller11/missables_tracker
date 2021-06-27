<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * Trait PlaythroughStepTrait
	 * @package App\Entity
	 * for use with {@see \App\Entity\PlaythroughStepInterface}
	 */
	trait PlaythroughStepTrait {

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="string", length=64)
		 */
		private ?string $name;

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="text")
		 */
		private ?string $description;

		//getters and setters

		/**
		 * @param string|null $name
		 * @return static
		 */
		public function setName(?string $name): static {
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
		 * @return string|null
		 */
		public function getName(): ?string {
			return $this->name;
		}

		/**
		 * @return string|null
		 */
		public function getDescription(): ?string {
			return $this->description;
		}

	}