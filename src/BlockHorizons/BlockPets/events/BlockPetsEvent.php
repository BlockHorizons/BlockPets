<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use pocketmine\event\plugin\PluginEvent;

abstract class BlockPetsEvent extends PluginEvent {

	public function __construct(private Loader $loader) {
		parent::__construct($loader);
	}

	public function getLoader(): Loader {
		return $this->loader;
	}

	public function getPlugin(): Loader {
		return $this->loader;
	}
}