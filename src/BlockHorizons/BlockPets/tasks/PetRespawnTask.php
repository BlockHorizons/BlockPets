<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\tasks;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

class PetRespawnTask extends BaseTask {

	private $pet;

	public function __construct(Loader $loader, BasePet $pet) {
		parent::__construct($loader);
		$this->pet = $pet;
	}

	public function onRun(int $currentTick) {
		$this->pet->spawnToAll();
		$this->pet->setDormant(false);
	}
}