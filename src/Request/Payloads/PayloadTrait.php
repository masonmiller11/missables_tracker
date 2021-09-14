<?php

	namespace App\Request\Payloads;

	trait PayloadTrait {

		/**
		 * @var \ReflectionClass|null
		 */
		private ?\ReflectionClass $reflectionClass = null;

		/**
		 * @var array
		 */
		private array $propertyCache = [];

		/**
		 * Tests is a property exists and has been initialized.
		 *
		 * Results are cached in a $properties array, so subsequent calls to this method for the same property should be extremely fast.
		 *
		 * Calling unset() directly on a property can cause the cache to desynchronize from the current state of the object.
		 * Use $this->unset() instead.
		 *
		 * @param string $property
		 * @param bool $useCache
		 *
		 * @return bool
		 * @throws \ReflectionException
		 */
		public function doesExist(string $property, bool $useCache):bool {
			if (!$this->reflectionClass)
				$this->reflectionClass = new \ReflectionClass(static::class);

			if ($useCache && isset($this->propertyCache[$property]))
				return $this->propertyCache[$property];

			return $this->propertyCache[$property] = $this->reflectionClass->getProperty($property)
				->isInitialized($this);
		}

		/**
		 * @param string $property
		 */
		public function unset(string $property) {
			unset($this->propertyCache[$property]);
			unset($this->{$property});
		}

	}