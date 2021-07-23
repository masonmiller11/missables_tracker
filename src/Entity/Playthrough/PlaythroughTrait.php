<?php
	namespace App\Entity\Playthrough;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * Trait PlaythroughTrait
	 * @package App\Entity\Playthrough
	 * for use with {@see PlaythroughInterface}
	 */
	trait PlaythroughTrait {

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
		 * @param string $name
		 * @return static
		 */
		public function setName(string $name): static {
			$this->name = $name;
			return $this;
		}

		/**
		 * @param string $description
		 * @return static
		 */
		public function setDescription(string $description): static {
			$this->description = $description;
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

	}