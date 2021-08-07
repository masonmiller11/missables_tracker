<?php
	namespace App\Entity\Step;

	use App\Entity\EntityTrait;
	use App\Entity\Section\Section;
	use App\Entity\User;
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
		 *
		 * @param string  $name
		 * @param string  $description
		 * @param Section $section
		 * @param int     $position
		 */
		public function __construct(string $name, string $description, Section $section, int $position) {

			$this->position = $position;
			$this->name = $name;
			$this->description = $description;
			$this->section = $section;

			$this->setCompleted(false);

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

		public function getOwner(): User {
			return $this->section->getOwner();
		}

		/**
		 * @return bool
		 */
		public function isCompleted(): bool {
			return $this->completed;
		}

	}