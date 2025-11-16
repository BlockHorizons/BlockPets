<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\PetData;

class PetRespawnEvent extends BlockPetsEvent {

	public function __construct(Loader $loader, private PetData $petData, private int $delay) {
		parent::__construct($loader);
	}

	public function getPetData(): PetData {
		return $this->petData;
	}

	/**
	 * Returns the delay in seconds for the pet to respawn.
	 */
	public function getDelay(): int {
		return $this->delay;
	}

	/**
	 * Sets the delay for a pet to respawn.
	 */
	public function setDelay(int $secondsDelay): void {
		$this->delay = $secondsDelay;
	}
}