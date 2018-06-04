<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

abstract class PetEvent extends BlockPetsEvent {

	/** @var BasePet */
	protected $pet;

	public function __construct(Loader $loader, BasePet $pet) {
		parent::__construct($loader);
		$this->pet = $pet;
	}

	/**
	 * Returns the pet that will be respawned.
	 *
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}
}