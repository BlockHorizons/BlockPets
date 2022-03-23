<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\tasks;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

class PetRespawnTask extends BaseTask{

	public function __construct(Loader $loader, private BasePet $pet) {
		parent::__construct($loader);
	}

	public function onRun(): void {
		$this->pet->spawnToAll();
		$this->pet->setDormant(false);
	}
}