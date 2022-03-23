<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\tasks;

use BlockHorizons\BlockPets\Loader;
use pocketmine\scheduler\Task;

abstract class BaseTask extends Task {

	public function __construct(protected Loader $loader) {
	}

	public function getLoader(): Loader {
		return $this->loader;
	}
}
