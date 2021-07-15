<?php
	namespace App\Entity\Step;

	use App\Entity\EntityTrait;
	use App\Entity\Section\Section;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthrough_steps")
	 */
	class Step implements StepInterface {

		use EntityTrait;

		use StepTrait;

		/**
		 * @var Section
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Section\Section", inversedBy="steps")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private Section $section;

		/**
		 * @var bool
		 *
		 * @ORM\Column(type="boolean")
		 */
		private bool $completed;

		/**
		 * Step constructor.
		 * @param string $name
		 * @param string $description
		 * @param Section $section
		 */
		public function __construct(string $name, string $description, Section $section) {

			$this->name = $name;
			$this->description = $description;
			$this->section = $section;

		}

		/**
		 * @param bool $completed
		 * @return static
		 */
		public function setCompleted(bool $completed): static {
			$this->completed = $completed;
			return $this;
		}

		/**
		 * @return Section
		 */
		public function getSection(): Section {
			return $this->section;
		}

		/**
		 * @return bool
		 */
		public function isCompleted(): bool {
			return $this->completed;
		}

	}