<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;
	use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
	use Symfony\Component\Security\Core\User\UserInterface;

	/**
	 * @ORM\Entity()
	 * @ORM\Table(name="users")
	 */
	class User implements PasswordAuthenticatedUserInterface, UserInterface {

		/**
		 * @var int|null
		 *
		 * @ORM\Id
		 * @ORM\GeneratedValue()
		 * @ORM\Column(type="integer", options={"unsigned": true})
		 */
		private ?int $id = null;

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

		public function __construct(string $email) {
			$this->email = $email;
		}

		/**
		 * @return string
		 */
		public function getEmail(): string {
			return $this->email;
		}

		/**
		 * @param string $email
		 */
		public function setEmail(string $email): void {
			$this->email = $email;
		}

		/**
		 * @return string|null
		 */
		public function getPassword(): ?string {
			return $this->password;
		}

		/**
		 * @param string|null $password
		 */
		public function setPassword(?string $password): void {
			$this->password = $password;
		}

		/**
		 * @return int|null
		 */
		public function getId(): ?int {
			return $this->id;
		}

		public function getRoles() :array {
			return ['ROLE_USER'];
		}

		public function getUsername() :string {
			return $this->getEmail();
		}

		public function getUserIdentifier() :string {
			return $this->getEmail();
		}

		public function getSalt() {
			//no op
		}

		public function eraseCredentials() {
			//no op
		}
	}