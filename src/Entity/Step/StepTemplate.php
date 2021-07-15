<?php
	namespace App\Entity\Step;

	use App\Entity\EntityTrait;
	use App\Entity\Section\SectionTemplate;
	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthrough_template_steps")
	 */
	class StepTemplate implements StepInterface {

		use EntityTrait;

		use StepTrait;

		/**
		 * @var SectionTemplate
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Playthrough\PlaythroughTemplate", inversedBy="stepTemplates")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private SectionTemplate $sectionTemplate;

		/**
		 * StepTemplate constructor.
		 * @param string $name
		 * @param string $description
		 * @param SectionTemplate $sectionTemplate
		 */
		public function __construct(string $name,
		                            string $description,
		                            SectionTemplate $sectionTemplate) {

			$this->name = $name;
			$this->description = $description;
			$this->sectionTemplate = $sectionTemplate;

		}

		/**
		 * @return SectionTemplate
		 */
		public function getSection(): SectionTemplate {
			return $this->sectionTemplate;
		}

	}