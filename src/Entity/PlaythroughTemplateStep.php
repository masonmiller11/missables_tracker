<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthrough_template_steps")
	 */
	class PlaythroughTemplateStep implements PlaythroughStepInterface {

		use EntityTrait;

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

		/**
		 * @var PlaythroughTemplate
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\PlaythroughTemplate", inversedBy="playthroughTemplateSteps")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private PlaythroughTemplate $playthroughTemplate;

		/**
		 * @var User
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="playthroughTemplateSteps")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private User $owner;

		public function __construct(string $name, string $description, PlaythroughTemplate $playthroughTemplate, User $owner) {

			$this->name = $name;
			$this->description = $description;
			$this->playthroughTemplate = $playthroughTemplate;
			$this->owner = $owner;

		}

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

		/**
		 * @return PlaythroughTemplate
		 */
		public function getPlaythrough(): PlaythroughTemplate {
			return $this->playthroughTemplate;
		}

		/**
		 * @return User
		 */
		public function getOwner(): User {
			return $this->owner;
		}




	}