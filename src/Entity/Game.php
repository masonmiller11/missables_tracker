<?php
	namespace App\Entity;

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
		 * @ORM\Column(type="string", length=64)
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
		 * @var string
		 * @see Genre
		 *
		 * @ORM\Column(type="string", length=64)
		 */
		private string $genre;

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

		#[Pure] public function __construct (string $genre,
		                                     string $title,
		                                     string $developer,
		                                     \DateTimeImmutable $releaseDate) {

			$this->playthroughTemplates = new ArrayCollection();
			$this->playthroughs = new ArrayCollection();

			$this->developer = $developer;
			$this->genre = $genre;
			$this->title = $title;
			$this->releaseDate = $releaseDate;

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
		
	}