<?php
	namespace App\Entity\Step;

	use App\Entity\EntityTrait;
	use App\Entity\Section\SectionTemplate;
	use App\Entity\User;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;

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
		 * @ORM\ManyToOne(targetEntity="App\Entity\Section\SectionTemplate", inversedBy="stepTemplates")
		 * @ORM\JoinColumn(nullable=false)
		 */
		private SectionTemplate $sectionTemplate;

		/**
		 * StepTemplate constructor.
		 * @param string $name
		 * @param string $description
		 * @param SectionTemplate $sectionTemplate
		 * @param int $position
		 */
		public function __construct(string $name,
		                            string $description,
		                            SectionTemplate $sectionTemplate,
									int $position) {

			$this->name = $name;
			$this->position = $position;
			$this->description = $description;
			$this->sectionTemplate = $sectionTemplate;

		}

		/**
		 * @return SectionTemplate
		 */
		public function getSection(): SectionTemplate {
			return $this->sectionTemplate;
		}

		/**
		 * @return User
		 */
		#[Pure]
		public function getOwner(): User {
			return $this->sectionTemplate->getOwner();
		}

	}