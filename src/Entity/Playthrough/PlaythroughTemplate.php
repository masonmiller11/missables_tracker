<?php
	namespace App\Entity\Playthrough;

	use App\Entity\EntityTrait;
	use App\Entity\Game;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\User;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthrough_templates")
	 */
	class PlaythroughTemplate implements PlaythroughInterface { //TODO add comments entity.

		use EntityTrait;

		use PlaythroughTrait;

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
		 * @var bool
		 *
		 * @ORM\Column(type="boolean")
		 */
		private bool $visibility;

		/**
		 * @var int
		 *
		 * @ORM\Column(type="integer")
		 */
		private int $votes;

		/**
		 * @var Collection|Selectable|SectionTemplate[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Section\SectionTemplate", mappedBy="playthroughTemplate", cascade={"all"}, orphanRemoval=true)
		 */
		private Collection|Selectable|array $sectionTemplates;

		/**
		 * @var Collection|Selectable|Playthrough[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Playthrough\Playthrough", mappedBy="template")
		 */
		private Collection|Selectable|array $playthroughs;

		/**
		 * PlaythroughTemplate constructor.
		 * @param User $owner
		 * @param Game $game
		 * @param bool $visibility
		 */
		#[Pure] public function __construct(string $name, string $description, User $owner, Game $game, bool $visibility) {

			$this->playthroughs = new ArrayCollection();
			$this->sectionTemplates = new ArrayCollection();

			$this->owner = $owner;
			$this->game = $game;
			$this->visibility = $visibility;
			$this->name = $name;
			$this->description = $description;
			$this->votes = 0;

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
		 * @param int $votes
		 * @return static
		 */
		public function setVotes(int $votes): static {
			$this->votes = $votes;
			return $this;
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
		 * @return int
		 */
		public function getVotes(): int {
			return $this->votes;
		}

		/**
		 * @return SectionTemplate[]|Collection|Selectable
		 */
		public function getSections(): Collection|array|Selectable {
			return $this->sectionTemplates;
		}

		/**
		 * @return Playthrough[]|Collection|Selectable
		 */
		public function getPlaythroughs(): Collection|array|Selectable {
			return $this->playthroughs;
		}

		/**
		 * @return bool
		 */
		public function isVisible(): bool {
			return $this->visibility;
		}

	}