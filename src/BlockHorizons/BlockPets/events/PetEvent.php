<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

abstract class PetEvent extends BlockPetsEvent {

	public function __construct(Loader $loader, protected BasePet $pet) {
		parent::__construct($loader);
	}

	/**
	 * Returns the pet that will be respawned.
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}
}