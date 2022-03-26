<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;

class PetProperties {

	private array $properties;

	public function __construct(private Loader $loader) {
		$loader->saveResource("pet_properties.yml");
		$this->collectProperties();
	}

	public function collectProperties(): void {
		$data = yaml_parse_file($this->getLoader()->getDataFolder() . "pet_properties.yml");
		$this->properties = $data;
	}

	public function getLoader(): Loader {
		return $this->loader;
	}

	public function getPropertiesFor(string $entityType): array {
		if(!$this->propertiesExistFor($entityType)) {
			return [];
		}
		return $this->properties[$entityType];
	}

	public function propertiesExistFor(string $entityType): bool {
		if(isset($this->properties[$entityType])) {
			return true;
		}
		return false;
	}
}