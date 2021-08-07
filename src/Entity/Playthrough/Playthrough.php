<?php
	namespace App\Entity\Playthrough;

	use App\Entity\EntityTrait;
	use App\Entity\Game;
	use App\Entity\Section\Section;
	use App\Entity\User;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthroughs")
	 */
	class Playthrough implements PlaythroughInterface {

		use EntityTrait;

		use PlaythroughTrait;

		/**
		 * @var Game
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="playthroughs")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private Game $game;

		/**
		 * @var int|null
		 *
		 * @ORM\Column(type="integer", options={"unsigned":true}, nullable=true)
		 */
		private ?int $templateId;

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
		 * @var Collection|Selectable|Section[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Section\Section", mappedBy="playthrough", cascade={"all"}, orphanRemoval=true)
		 */
		private Collection|Selectable|array $sections;

		/**
		 * Playthrough constructor.
		 * @param string $name
		 * @param string $description
		 * @param Game $game
		 * @param int $templateId
		 * @param User $owner
		 * @param bool $visibility
		 */
		#[Pure] public function __construct(string $name,
		                                    string $description,
		                                    Game $game,
										    int $templateId,
											User $owner,
											bool $visibility) {

			$this->sections = new ArrayCollection();

			$this->game = $game;
			$this->templateId = $templateId;
			$this->owner = $owner;
			$this->visibility = $visibility;
			$this->name = $name;
			$this->description = $description;

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
		 * @param int|null $id
		 * @return static
		 */
		public function setTemplateId(?int $id): static {
			$this->templateId = $id;
			return $this;
		}

		/**
		 * @return Game
		 */
		public function getGame(): Game {
			return $this->game;
		}

		/**
		 * @return int|null
		 */
		public function getTemplateId(): ?int {
			return $this->templateId;
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
		 * @return Section[]|Collection|Selectable
		 */
		public function getSections(): Collection|array|Selectable {
			return $this->sections;
		}

	}