<?php
	namespace App\Entity;

	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\Validator\Constraints as Assert;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\GameRepository")
	 * @ORM\Table(name="games")
	 */
	class Game implements EntityInterface {//TODO create a CustomGame entity

		use EntityTrait;

		/**
		 * @ORM\Column(type="string", nullable=false, length=128)
		 *
		 * @Assert\NotBlank()
		 *
		 * @var string
		 */
		private string $title;

		/**
		 * @ORM\Column(type="datetime_immutable", nullable=false)
		 *
		 * @var \DateTimeImmutable
		 */
		private \DateTimeImmutable $releaseDate;

		/**
		 * @var array|null
		 *
		 * @ORM\Column(type="simple_array", nullable=true)
		 */
		private ?array $genres;

		/**
		 * @var float|null
		 *
		 * @ORM\Column(type="float", nullable=true)
		 */
		private ?float $rating;

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="text", nullable=true)
		 */
		private ?string $summary;

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="text", nullable=true)
		 */
		private ?string $storyline;

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="string", length=64)
		 */
		private ?string $slug;

		/**
		 * @var array|null
		 *
		 * @ORM\Column(type="simple_array", nullable=true)
		 */
		private ?array $screenshots;

		/**
		 * @var array|null
		 *
		 * @ORM\Column(type="simple_array", nullable=true)
		 */
		private ?array $platforms;

		//TODO eventually we want to save the cover's URL so we aren't constantly pinging IGDB
		/**
		 * @var string
		 *
		 * @Assert\NotBlank()
		 *
		 * @ORM\Column(type="string", length=64, nullable=false)
		 */
		private string $cover;

		/**
		 * @var array|null
		 *
		 * @ORM\Column(type="simple_array", nullable=true)
		 */
		private ?array $artworks;

		/**
		 * @var int
		 *
		 * @ORM\Column(type="integer", unique=true, options={"unsigned":true}, nullable=false)
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
		 *
		 * @param array|null $genres
		 * @param string $title
		 * @param int $internetGameDatabaseID
		 * @param array|null $screenshots
		 * @param array|null $artworks
		 * @param string $cover
		 * @param array|null $platforms
		 * @param string|null $slug
		 * @param float|null $rating
		 * @param string|null $summary
		 * @param string|null $storyline
		 * @param \DateTimeImmutable $releaseDate
		 */
		#[Pure] public function __construct(?array $genres,
		                                    string $title,
		                                    int $internetGameDatabaseID,
		                                    ?array $screenshots,
		                                    ?array $artworks,
		                                    string $cover,
		                                    ?array $platforms,
		                                    ?string $slug,
		                                    ?float $rating,
		                                    ?string $summary,
		                                    ?string $storyline,
		                                    \DateTimeImmutable $releaseDate) {

			$this->playthroughTemplates = new ArrayCollection();
			$this->playthroughs = new ArrayCollection();

			$this->genres = $genres;
			$this->screenshots = $screenshots;
			$this->artworks = $artworks;
			$this->cover = $cover;
			$this->platforms = $platforms;
			$this->slug = $slug;
			$this->rating = $rating;
			$this->summary = $summary;
			$this->title = $title;
			$this->storyline = $storyline;
			$this->releaseDate = $releaseDate;
			$this->internetGameDatabaseID = $internetGameDatabaseID;

		}

		/**
		 * @return string
		 */
		public function getTitle(): string {
			return $this->title;
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
		 * @return \DateTimeImmutable
		 */
		public function getReleaseDate(): \DateTimeImmutable {
			return $this->releaseDate;
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
		 * @return string
		 */
		public function getGenre(): string {
			return $this->genre;
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
		 * @param int $internetGameDatabaseID
		 * @return static
		 */
		public function setInternetGameDatabaseID(int $internetGameDatabaseID): static {
			$this->internetGameDatabaseID = $internetGameDatabaseID;
			return $this;
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

		/**
		 * @return int
		 */
		public function getPlaythroughTemplateCount(): int {
			return $this->playthroughTemplates->count();
		}

		/**
		 * @return int
		 */
		public function getPlaythroughCount(): int {
			return $this->playthroughs->count();
		}

	}