<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

class PetInventoryInitializationEvent extends PetEvent {

	/**
	 * Returns the name of the owner of the pet.
	 */
	public function getOwnerName(): string {
		return $this->pet->getPetOwnerName();
	}
}