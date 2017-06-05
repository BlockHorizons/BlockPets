<?php

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;

class PetMessage {

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