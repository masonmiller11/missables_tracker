<?php
	namespace App\Entity;

	use Doctrine\Common\Collections\ArrayCollection;
	use Doctrine\Common\Collections\Collection;
	use Doctrine\Common\Collections\Selectable;
	use Doctrine\ORM\Mapping as ORM;
	use JetBrains\PhpStorm\Pure;
	use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
	use Symfony\Component\Security\Core\User\UserInterface;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="users")
	 */
	class User implements PasswordAuthenticatedUserInterface, UserInterface {

		use EntityTrait;

		/**
		 * @var string
		 *
		 * @ORM\Column(type="string", length=254, unique=true)
		 */
		private string $email;

		/**
		 * @var string|null
		 *
		 * @ORM\Column(type="text", nullable=true)
		 */
		private ?string $password = null;

		/**
		 * @var Collection|Selectable|PlaythroughTemplate[]
		 *
		 * @ORM\OneToMany(targetEntity="App\Entity\PlaythroughTemplate", mappedBy="owner")
		 */
		private Collection|Selectable|array $playthroughTemplates;

		#[Pure] public function __construct(string $email) {

			$this->email = $email;

			$this->playthroughTemplates = new ArrayCollection();
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
		 * @return PlaythroughTemplate[]|Collection|Selectable
		 */
		public function getPlaythroughTemplates(): Collection|array|Selectable {
			return $this->playthroughTemplates;
		}

		/**
		 * @return string
		 */
		public function getEmail(): string {
			return $this->email;
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

		#[Pure] public function getUsername() :string {
			return $this->getEmail();
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