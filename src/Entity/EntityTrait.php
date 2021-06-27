<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * Trait EntityTrait
	 * @package App\Entity
	 * for use with {@see EntityInterface}
	 */
	trait EntityTrait {

		/**
		 * @ORM\Id()
		 * @ORM\GeneratedValue()
		 * @ORM\Column(type="integer", options={"unsigned":true})
		 *
		 * @var int|null
		 */
		protected ?int $id = null;

		/**
		 * @return int|null
		 */
		public function getId(): ?int {
			return $this->id;
		}

	}