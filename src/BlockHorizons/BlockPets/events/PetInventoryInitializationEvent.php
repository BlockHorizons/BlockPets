<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use pocketmine\event\Cancellable;

class PetInventoryInitializationEvent extends BlockPetsEvent implements Cancellable {

	/**
	 * Returns the name of the owner of the pet.
	 *
	 * @return string
	 */
	public function getOwnerName(): string {
		return $this->pet->getPetOwnerName();
	}
}