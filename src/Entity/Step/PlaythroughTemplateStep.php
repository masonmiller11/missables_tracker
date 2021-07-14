<?php
	namespace App\Entity\Step;

	use App\Entity\EntityTrait;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\User;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthrough_template_steps")
	 */
	class PlaythroughTemplateStep implements PlaythroughStepInterface {

		use EntityTrait;

		use PlaythroughStepTrait;

		/**
		 * @var PlaythroughTemplate
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\PlaythroughTemplate", inversedBy="playthroughTemplateSteps")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private PlaythroughTemplate $template;

		/**
		 * @var User
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="playthroughTemplateSteps")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private User $owner;

		public function __construct(string $name,
		                            string $description,
		                            PlaythroughTemplate $playthroughTemplate,
		                            User $owner) {

			$this->name = $name;
			$this->description = $description;
			$this->playthroughTemplate = $playthroughTemplate;
			$this->owner = $owner;

		}

		/**
		 * @return PlaythroughTemplate
		 */
		public function getPlaythrough(): PlaythroughTemplate {
			return $this->template;
		}

		/**
		 * @return User
		 */
		public function getOwner(): User {
			return $this->owner;
		}

	}