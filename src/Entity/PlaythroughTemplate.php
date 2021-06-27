<?php
	namespace App\Entity;

	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	class PlaythroughTemplate implements EntityInterface {

		use EntityTrait;

		/**
		 * @var Game
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="playthroughTemplates")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private Game $game;

		/**
		 * @var User
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="playthroughTemplates")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private User $owner;

		/**
		 * @var Collection|Selectable|PlaythroughTemplateStep[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\PlaythroughTemplateStep", mappedBy="template", cascade={"all"}, orphanRemoval=true)
		 */
		private Collection|Selectable|array $playthroughTemplateSteps;

		/**
		 * @var Collection|Selectable|Playthrough[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Playthrough", mappedBy="template")
		 */
		private Collection|Selectable|array $playthroughs;

		#[Pure] public function __construct(User $owner, Game $game) {

			$this->playthroughs = new ArrayCollection();
			$this->playthroughTemplateSteps = new ArrayCollection();

			$this->owner = $owner;
			$this->game = $game;

		}

		/**
		 * @return Game
		 */
		public function getGame(): Game {
			return $this->game;
		}

		/**
		 * @return User
		 */
		public function getOwner(): User {
			return $this->owner;
		}

		/**
		 * @return PlaythroughTemplateStep[]|Collection|Selectable
		 */
		public function getPlaythroughTemplateSteps(): Collection|array|Selectable {
			return $this->playthroughTemplateSteps;
		}

		/**
		 * @return Playthrough[]|Collection|Selectable
		 */
		public function getPlaythroughs(): Collection|array|Selectable {
			return $this->playthroughs;
		}

	}