<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\tasks;

use BlockHorizons\BlockPets\Loader;
use pocketmine\scheduler\Task;

abstract class BaseTask extends Task {

	protected $loader;

	public function __construct(Loader $loader) {
		
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}
