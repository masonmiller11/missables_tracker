<?php
	namespace App\Entity;

	use Doctrine\ORM\Mapping as ORM;

	/**
	 * @ORM\Entity(repositoryClass="App\Repository\IGDBConfigRepository")
	 *
	 * @ORM\Table(name="igdb_config")
	 */
	class IGDBConfig implements EntityInterface {

		use EntityTrait;

		/**
		 * @ORM\Column(type="string", length=128)
		 *
		 * @var string
		 */
		private string $token;

		/**
		 * @ORM\Column(type="datetime_immutable")
		 *
		 * @var \DateTimeImmutable
		 */
		private \DateTimeImmutable $expiration;

		/**
		 * @ORM\Column(type="datetime_immutable")
		 *
		 * @var \DateTimeImmutable
		 */
		private \DateTimeImmutable $generatedAt;

		public function __construct(string $token, \DateTimeImmutable $expiration, \DateTimeImmutable $generatedAt) {

			$this->token = $token;
			$this->expiration = $expiration;
			$this->generatedAt = $generatedAt;

		}

		/**
		 * @param string $token
		 *
		 * @return IGDBConfig
		 */
		public function setToken(string $token): static {
			$this->token = $token;
			return $this;
		}

		/**
		 * @param \DateTimeImmutable $expiration
		 *
		 * @return IGDBConfig
		 */
		public function setExpiration(\DateTimeImmutable $expiration): static {
			$this->expiration = $expiration;
			return $this;
		}

		/**
		 * @param \DateTimeImmutable $generatedAt
		 *
		 * @return IGDBConfig
		 */
		public function setGeneratedAt(\DateTimeImmutable $generatedAt): static {
			$this->generatedAt = $generatedAt;
			return $this;
		}

		/**
		 * @return string
		 */
		public function getToken(): string {
			return $this->token;
		}

		/**
		 * @return \DateTimeImmutable
		 */
		public function getExpiration(): \DateTimeImmutable {
			return $this->expiration;
		}

		/**
		 * @return \DateTimeImmutable
		 */
		public function getGeneratedAt(): \DateTimeImmutable {
			return $this->generatedAt;
		}

	}