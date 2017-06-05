<?php

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use pocketmine\event\plugin\PluginEvent;

abstract class BlockPetsEvent extends PluginEvent {

	private $loader;

	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}