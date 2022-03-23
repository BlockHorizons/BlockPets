<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

class PetRemoveEvent extends PetEvent {

	/**
	 * Returns the owner of the pet about to be spawned.
	 */
	public function getPlayerName(): string {
		return $this->pet->getPetOwnerName();
	}
}