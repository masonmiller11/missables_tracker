<?php
	namespace App\Entity;

	use App\Entity\Playthrough\Playthrough;
	use App\Entity\Playthrough\PlaythroughTemplate;
	use App\Entity\Playthrough\PlaythroughTemplateLike;
	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
	use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
	use Symfony\Component\Security\Core\User\UserInterface;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="users")
	 * @UniqueEntity(
	 *      fields={"email"},
	 *      errorPath="email",
	 *      message="It appears you have already registered with this email."
	 *)
	 * @UniqueEntity(
	 *      fields={"username"},
	 *      errorPath="username",
	 *      message="Username is already taken."
	 *)
	 */
	class User implements PasswordAuthenticatedUserInterface, UserInterface, EntityInterface {

		use EntityTrait;

		/**
		 * @var string
		 *
		 * @ORM\Column(type="string", length=254, unique=true)
		 */
		private string $email;

		/**
		 * @var string
		 *
		 * @ORM\Column(type="string", length=254, unique=true)
		 */
		private string $username;

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="text", nullable=true)
		 */
		private ?string $password = null;

		/**
		 * @var Collection|Selectable|PlaythroughTemplate[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Playthrough\PlaythroughTemplate", mappedBy="owner")
		 */
		private Collection|Selectable|array $playthroughTemplates;

		/**
		 * @var Collection|Selectable|Playthrough[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Playthrough\Playthrough", mappedBy="owner", cascade={"all"}, orphanRemoval=true)
		 */
		private Collection|Selectable|array $playthroughs;

		/**
		 * @var Collection|Selectable|PlaythroughTemplateLike[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\Playthrough\PlaythroughTemplateLike", mappedBy="likedBy", cascade={"all"}, orphanRemoval=true)
		 */
		private Collection|Selectable|array $likes;

		/**
		 * User constructor.
		 * @param string $email
		 * @param string $username
		 */
		#[Pure] public function __construct(string $email, string $username) {

			$this->email = $email;
			$this->username = $username;

			$this->playthroughTemplates = new ArrayCollection();
			$this->playthroughs = new ArrayCollection();
			$this->likes = new ArrayCollection();

		}

		/**
		 * @param string $email
		 */
		public function setEmail(string $email): void {
			$this->email = $email;
		}

		/**
		 * @param string|null $password
		 */
		public function setPassword(?string $password): void {
			$this->password = $password;
		}

		/**
		 * @param string $username
		 */
		public function setUsername(string $username): void {
			$this->username = $username;
		}

		/**
		 * @return PlaythroughTemplate[]|Collection|Selectable
		 */
		public function getPlaythroughTemplates(): Collection|array|Selectable {
			return $this->playthroughTemplates;
		}

		/**
		 * @return Playthrough[]|Collection|Selectable
		 */
		public function getPlaythroughs(): Collection|array|Selectable {
			return $this->playthroughs;
		}

		/**
		 * @return PlaythroughTemplateLike[]|Collection|Selectable
		 */
		public function getLikes(): Collection|array|Selectable {
			return $this->likes;
		}

		/**
		 * @return string
		 */
		public function getEmail(): string {
			return $this->email;
		}

		/**
		 * @return string
		 * Security depends on this method returning email.
		 * Create a second method for returning username if needed.
		 */
		#[Pure]
		public function getUsername() :string {
			return $this->getEmail();
		}

		/**
		 * @return string
		 */
		public function getHandle() :string {
			return $this->username;
		}

		/**
		 * @return string|null
		 */
		public function getPassword(): ?string {
			return $this->password;
		}

		public function getRoles() :array {
			return ['ROLE_USER'];
		}

		#[Pure] public function getUserIdentifier() :string {
			return $this->getEmail();
		}

		public function getSalt() {
			//no op
		}

		public function eraseCredentials() {
			//no op
		}
	}