<?php
	namespace App\Entity;

	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="genres")
	 */
	class Genre implements EntityInterface {

		use EntityTrait;

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="string", length=64)
		 */
		private ?string $name;

		/**
		 * @var Collection|Game[]|Selectable
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Game", mappedBy="genre")
		 */
		private Collection|Selectable|array $games;

		#[Pure] public function __construct() {

			$this->games = new ArrayCollection();

		}

		/**
		 * @return Collection|Game[]|Selectable
		 */
		public function getGames(): Collection|Selectable|array {
			return $this->games;
		}

	}