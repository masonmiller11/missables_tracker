<?php
	namespace App\Entity\Section;

	use App\Entity\EntityTrait;
	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Step\Step;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="sections")
	 */
	class Section implements SectionInterface {

		use EntityTrait;
		use SectionTrait;

		/**
		 * @var Playthrough
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Playthrough\Playthrough", inversedBy="sections")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private Playthrough $playthrough;

		/**
		 * @var Collection|Selectable|Step[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Step\Step", mappedBy="section", cascade={"all"}, orphanRemoval=true)
		 */
		private Collection|Selectable|array $steps;

		/**
		 * Section constructor.
		 * @param string $name
		 * @param string $description
		 * @param Playthrough $playthrough
		 */
		#[Pure]
		public function __construct(string $name, string $description, Playthrough $playthrough) {

			$this->steps = new ArrayCollection();

			$this->name = $name;
			$this->description = $description;
			$this->playthrough = $playthrough;

		}

		/**
		 * @return Playthrough
		 */
		public function getPlaythrough(): Playthrough {
			return $this->playthrough;
		}

		/**
		 * @return Step[]|Collection|Selectable
		 */
		public function getSteps(): array|Collection|Selectable {
			return $this->steps;
		}

	}