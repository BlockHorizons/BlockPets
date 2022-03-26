<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;

class PetMessages {

	private array $messages = [];

	public function __construct(private Loader $loader) {
	}

	public function collectMessages(): void {
		$data = yaml_parse_file($this->getLoader()->getDataFolder() . "pet_messages.yml");
		$this->messages = $data;
	}

	public function getLoader(): Loader {
		return $this->loader;
	}
}