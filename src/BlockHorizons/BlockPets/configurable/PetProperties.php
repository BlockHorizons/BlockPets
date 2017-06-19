<?php

namespace BlockHorizons\BlockPets\configurable;


use BlockHorizons\BlockPets\Loader;

class PetProperties {

	private $loader;
	private $properties;

	public function __construct(Loader $loader) {
		$this->loader = $loader;

		$loader->saveResource("pet_properties.yml");
		$this->collectProperties();
	}

	public function collectProperties() {
		$data = yaml_parse_file($this->getLoader()->getDataFolder() . "pet_properties.yml");
		$this->properties = $data;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @param string $entityType
	 *
	 * @return array
	 */
	public function getPropertiesFor(string $entityType): array {
		if(!$this->propertiesExistFor($entityType)) {
			return [];
		}
		return $this->properties[$entityType];
	}

	/**
	 * @param string $entityType
	 *
	 * @return bool
	 */
	public function propertiesExistFor(string $entityType): bool {
		if(isset($this->properties[$entityType])) {
			return true;
		}
		return false;
	}
}