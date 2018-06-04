<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;

class PetRemoveEvent extends PetEvent {

	/**
	 * Returns the owner of the pet about to be spawned.
	 *
	 * @return string
	 */
	public function getPlayerName(): string {
		return $this->pet->getPetOwnerName();
	}
}