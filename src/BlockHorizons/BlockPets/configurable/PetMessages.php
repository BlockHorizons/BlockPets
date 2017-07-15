<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;

class PetMessages {

	private $loader;
	private $messages = [];

	public function __construct(Loader $loader) {
		$this->loader = $loader;
	}

	public function collectMessages() {
		$data = yaml_parse_file($this->getLoader()->getDataFolder() . "pet_messages.yml");
		$this->messages = $data;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}