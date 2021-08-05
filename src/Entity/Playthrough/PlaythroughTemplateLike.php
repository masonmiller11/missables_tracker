<?php
	namespace App\Entity\Playthrough;

	use App\Entity\EntityInterface;
	use App\Entity\EntityTrait;
	use App\Entity\User;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use Doctrine\ORM\Query\Expr\Select;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="playthrough_template_likes")
	 */
	class PlaythroughTemplateLike implements EntityInterface {

		use EntityTrait;

		/**
		 * @var User
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="likes")
		 */
		private User $likedBy;

		/**
		 * @var PlaythroughTemplate
		 *
		 * @ORM\ManyToOne(targetEntity="App\Entity\Playthrough\PlaythroughTemplate", inversedBy="likes")
		 */
		private PlaythroughTemplate $likedTemplate;

		public function __construct( User $likedBy, PlaythroughTemplate $likedTemplate) {

			$this->likedBy = $likedBy;
			$this->likedTemplate = $likedTemplate;

		}

		/**
		 * @param User $likedBy
		 * @return static
		 */
		public function setLikedBy(User $likedBy): static {
			$this->likedBy = $likedBy;
			return $this;
		}

		/**
		 * @param PlaythroughTemplate $likedTemplate
		 * @return static
		 */
		public function setLikedTemplate(PlaythroughTemplate $likedTemplate): static {
			$this->likedTemplate = $likedTemplate;
			return $this;
		}

		/**
		 * @return User
		 */
		public function getLikedBy(): User {
			return $this->likedBy;
		}

		/**
		 * @return PlaythroughTemplate
		 */
		public function getLikedTemplate(): PlaythroughTemplate {
			return $this->likedTemplate;
		}

	}