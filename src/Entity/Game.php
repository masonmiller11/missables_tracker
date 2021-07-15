<?php
	namespace App\Entity;

	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;
	use App\Genre;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
	 * @ORM\Table(name="games")
	 */
	class Game implements EntityInterface {//TODO create a CustomGame entity

		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string
		 */
		private string $title;

		/**
		 * @ORM\Column(type="datetime_immutable")
		 *
		 * @var \DateTimeImmutable
		 */
		private \DateTimeImmutable $releaseDate;

		/**
		 * @var string
		 * @see Genre
		 *
		 * @ORM\Column(type="string", length=64)
		 */
		private string $genre;

		/**
		 * @var float
		 *
		 * @ORM\Column(type="float", nullable=true)
		 */
		private float $rating;

		/**
		 * @var string
		 *
		 * @ORM\Column(type="text", nullable=true)
		 */
		private string $summary;

		/**
		 * @var string
		 *
		 * @ORM\Column(type="text", nullable=true)
		 */
		private string $storyline;

		/**
		 * @var string
		 *
		 * @ORM\Column(type="string", length=64)
		 */
		private string $slug;

		/**
		 * @var array
		 *
		 * @ORM\Column(type="simple_array", nullable=true)
		 */
		private array $screenshots;

		/**
		 * @var array
		 *
		 * @ORM\Column(type="simple_array", nullable=true)
		 */
		private array $platforms;

		/**
		 * @var string
		 *
		 * @ORM\Column(type="string", length=64, nullable=true)
		 */
		private string $cover;

		/**
		 * @var array
		 *
		 * @ORM\Column(type="simple_array", nullable=true)
		 */
		private array $artworks;

		/**
		 * @var int
		 *
		 * @ORM\Column(type="integer", unique=true, options={"unsigned":true})
		 */
		private int $internetGameDatabaseID;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\Playthrough\PlaythroughTemplate", mappedBy="game", cascade={"all"}, orphanRemoval=true)
		 *
		 * @var Collection|PlaythroughTemplate[]|Selectable
		 */
		private Collection|Selectable|array $playthroughTemplates;

		/**
		 * @ORM\OneToMany(targetEntity="App\Entity\Playthrough\Playthrough", mappedBy="game")
		 *
		 * @var Collection|Playthrough[]|Selectable
		 */
		private Collection|Selectable|array $playthroughs;

		/**
		 * Game constructor.
		 * @param string $genre
		 * @param string $title
		 * @param int $internetGameDatabaseID
		 * @param array $screenshots
		 * @param array $artworks
		 * @param string $cover
		 * @param array $platforms
		 * @param string $slug
		 * @param float $rating
		 * @param string $summary
		 * @param string $storyline
		 * @param \DateTimeImmutable $releaseDate
		 */
		#[Pure] public function __construct (string $genre,
		                                     string $title,
		                                     int $internetGameDatabaseID,
		                                     array $screenshots,
		                                     array $artworks,
		                                     string $cover,
		                                     array $platforms,
		                                     string $slug,
		                                     float $rating,
		                                     string $summary,
		                                     string $storyline,
		                                     \DateTimeImmutable $releaseDate) {

			$this->playthroughTemplates = new ArrayCollection();
			$this->playthroughs = new ArrayCollection();

			$this->screenshots = $screenshots;
			$this->artworks = $artworks;
			$this->cover = $cover;
			$this->platforms = $platforms;
			$this->slug = $slug;
			$this->rating =$rating;
			$this->summary = $summary;
			$this->genre = $genre;
			$this->title = $title;
			$this->storyline = $storyline;
			$this->releaseDate = $releaseDate;
			$this->internetGameDatabaseID = $internetGameDatabaseID;

		}

		/**
		 * @param string $title
		 * @return static
		 */
		public function setTitle(string $title): static {
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
		 * @param int $internetGameDatabaseID
		 * @return static
		 */
		public function setInternetGameDatabaseID(int $internetGameDatabaseID): static {
			$this->internetGameDatabaseID = $internetGameDatabaseID;
			return $this;
		}

		/**
		 * @param string $genre
		 * @return static
		 */
		public function setGenre(string $genre): static {
			$this->genre = $genre;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getTitle(): string {
			return $this->title;
		}

		/**
		 * @return \DateTimeImmutable
		 */
		public function getReleaseDate(): \DateTimeImmutable {
			return $this->releaseDate;
		}

		/**
		 * @return string
		 */
		public function getGenre(): string {
			return $this->genre;
		}

		/**
		 * @return PlaythroughTemplate[]|Collection|Selectable
		 */
		public function getTemplates(): Collection|array|Selectable {
			return $this->playthroughTemplates;
		}

		/**
		 * @return int
		 */
		public function getInternetGameDatabaseID(): int {
			return $this->internetGameDatabaseID;
		}

		/**
		 * @return mixed
		 */
		public function getRating(): float {
			return $this->rating;
		}

		/**
		 * @return string
		 */
		public function getSummary(): string {
			return $this->summary;
		}

		/**
		 * @return string
		 */
		public function getStoryline(): string {
			return $this->storyline;
		}

		/**
		 * @return string
		 */
		public function getSlug(): string {
			return $this->slug;
		}

		/**
		 * @return array
		 */
		public function getScreenshots(): array {
			return $this->screenshots;
		}

		/**
		 * @return array
		 */
		public function getPlatforms(): array {
			return $this->platforms;
		}

		/**
		 * @return string
		 */
		public function getCover(): string {
			return $this->cover;
		}

		/**
		 * @return array
		 */
		public function getArtworks(): array {
			return $this->artworks;
		}



	}