<?php
	namespace App\Entity\Step;

	use App\Entity\EntityTrait;
	use App\Entity\Playthrough\Playthrough;
	use App\Entity\User;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthrough_steps")
	 */
	class PlaythroughStep implements PlaythroughStepInterface {

		use EntityTrait;

		use PlaythroughStepTrait;

		/**
		 * @var User
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="playthroughSteps")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private User $owner;

		/**
		 * @var Playthrough
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Playthrough", inversedBy="steps")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private Playthrough $playthrough;

		/**
		 * @var bool
		 *
		 * @ORM\Column(type="boolean")
		 */
		private bool $completed;

		public function __construct(string $name, string $description, Playthrough $playthrough, User $owner) {

			$this->name = $name;
			$this->description = $description;
			$this->playthrough = $playthrough;
			$this->owner = $owner;

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
		 * @return User
		 */
		public function getOwner(): User {
			return $this->owner;
		}

		/**
		 * @return Playthrough
		 */
		public function getPlaythrough(): Playthrough {
			return $this->playthrough;
		}

		/**
		 * @return bool
		 */
		public function isCompleted(): bool {
			return $this->completed;
		}

	}