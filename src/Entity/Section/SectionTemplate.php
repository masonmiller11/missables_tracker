<?php
	namespace App\Entity\Section;

	use App\Entity\EntityTrait;
	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Step\Step;
	use App\Entity\Step\StepTemplate;
	use App\Entity\User;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="section_templates")
	 */
	class SectionTemplate implements SectionInterface {

		use EntityTrait;
		use SectionTrait;

		/**
		 * @var PlaythroughTemplate
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Playthrough\PlaythroughTemplate", inversedBy="sections")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private PlaythroughTemplate $playthroughTemplate;

		/**
		 * @var Collection|Selectable|StepTemplate[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Step\StepTemplate", mappedBy="sectionTemplate", cascade={"all"}, orphanRemoval=true)
		 */
		private Collection|Selectable|array $stepTemplates;

		/**
		 * Section constructor.
		 * @param string $name
		 * @param string $description
		 * @param PlaythroughTemplate $playthroughTemplate
		 */
		#[Pure]
		public function __construct(string $name, string $description, PlaythroughTemplate $playthroughTemplate) {

			$this->stepTemplates = new ArrayCollection();

			$this->name = $name;
			$this->description = $description;
			$this->playthroughTemplate = $playthroughTemplate;
		}

		/**
		 * @return PlaythroughTemplate
		 */
		public function getPlaythrough(): PlaythroughTemplate {
			return $this->playthroughTemplate;
		}

		/**
		 * @return StepTemplate[]|Collection|Selectable
		 */
		public function getSteps(): array|Collection|Selectable {
			return $this->stepTemplates;
		}

	}