<?php
	namespace App\Entity;

	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthroughts")
	 */
	class Playthrough implements PlaythroughInterface {

		use EntityTrait;

		/**
		 * @var Game
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="playthroughTemplates")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private Game $game;

		/**
		 * @var \App\Entity\PlaythroughTemplate
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\PlaythroughTemplate", inversedBy="playthroughs")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private PlaythroughTemplate $template;

		/**
		 * @var User
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="playthroughs")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private User $owner;

		/**
		 * @var bool
		 *
		 * @ORM\Column(type="boolean")
		 */
		private bool $visibility;

		/**
		 * @var Collection|Selectable|PlaythroughStep[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\PlaythroughStep", mappedBy="playthrough", cascade={"all"}, orphanRemoval=true)
		 */
		private Collection|Selectable|array $steps;

		#[Pure] public function __construct(Game $game, PlaythroughTemplate $template, User $owner, bool $visibility) {

			$this->steps = new ArrayCollection();

			$this->game = $game;
			$this->template = $template;
			$this->owner = $owner;
			$this->visibility = $visibility;

		}

		/**
		 * @param bool $visibility
		 * @return static
		 */
		public function setVisibility(bool $visibility): static {
			$this->visibility = $visibility;
			return $this;
		}

		/**
		 * @return Game
		 */
		public function getGame(): Game {
			return $this->game;
		}

		/**
		 * @return PlaythroughTemplate
		 */
		public function getTemplate(): PlaythroughTemplate {
			return $this->template;
		}

		/**
		 * @return User
		 */
		public function getOwner(): User {
			return $this->owner;
		}

		/**
		 * @return bool
		 */
		public function isVisible(): bool {
			return $this->visibility;
		}

		/**
		 * @return PlaythroughStep[]|Collection|Selectable
		 */
		public function getSteps(): array|Collection|Selectable {
			return $this->steps;
		}

	}