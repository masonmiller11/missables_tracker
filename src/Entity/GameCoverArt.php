<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="game_cover_art")
	 */
	class GameCoverArt implements EntityInterface {

		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string
		 */
		private string $uri;

		/**
		 * @var Game
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Game", inversedBy="coverArt")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private Game $game;


		/**
		 * Game constructor.
		 * @param string $uri
		 * @param Game $game
		 */
		#[Pure] public function __construct(string $uri,
		                                    Game $game) {
			$this->uri = $uri;
			$this->game = $game;
		}

		/**
		 * @return Game
		 */
		public function getGame(): Game {
			return $this->game;
		}

		/**
		 * @param Game $game
		 * @return static
		 */
		public function setGame(Game $game): static {
			$this->game = $game;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getUri(): string {
			return $this->uri;
		}

		/**
		 * @param string $uri
		 * @return static
		 */
		public function setUri(string $uri): static {
			$this->uri = $uri;
			return $this;
		}

	}