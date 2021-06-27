<?php
	namespace App\Entity;

	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="games")
	 */
	class Game implements EntityInterface {

		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string|null
		 */
		private ?string $title;

		/**
		 * @ORM\Column(type="string", length 64)
		 *
		 * @var string|null
		 */
		private ?string $developer; //TODO let's create an entity for developer and have this be a many to one rel

		/**
		 * @ORM\Column(type="datetime_immutable")
		 *
		 * @var \DateTimeImmutable
		 */
		private \DateTimeImmutable $releaseDate;

		/**
		 * @var Genre
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Genre", inversedBy="games")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private Genre $genre;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\PlaythroughTemplate", mappedBy="game", cascade={"all"}, orphanRemoval=true)
		 *
		 * @var Collection|PlaythroughTemplate[]|Selectable
		 */
		private Collection|Selectable|array $playthroughTemplates;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\Playthrough", mappedBy="game")
		 *
		 * @var Collection|Playthrough[]|Selectable
		 */
		private Collection|Selectable|array $playthroughs;

		#[Pure] public function __construct (Genre $genre, string $title, \DateTimeImmutable $releaseDate) {//TODO add developer to construct sig

			$this->playthroughTemplates = new ArrayCollection();
			$this->playthroughs = new ArrayCollection();

			$this->genre = $genre;
			$this->title = $title;
			$this->releaseDate = $releaseDate;

		}

		/**
		 * @param string|null $title
		 * @return static
		 */
		public function setTitle(?string $title): static {
			$this->title = $title;
			return $this;
		}

		/**
		 * @param \DateTimeImmutable $releaseDate
		 * @return static
		 */
		public function setReleaseDate(\DateTimeImmutable $releaseDate): static {
			$this->releaseDate = $releaseDate;
			return $this;
		}

		/**
		 * @return string|null
		 */
		public function getTitle(): ?string {
			return $this->title;
		}

		/**
		 * @return \DateTimeImmutable
		 */
		public function getReleaseDate(): \DateTimeImmutable {
			return $this->releaseDate;
		}

		/**
		 * @return Genre
		 */
		public function getGenre(): Genre {
			return $this->genre;
		}
		
	}