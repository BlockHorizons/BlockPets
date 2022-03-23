<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class PetInventoryInitializationEvent extends PetEvent implements Cancellable {

	use CancellableTrait;

	/**
	 * Returns the name of the owner of the pet.
	 */
	public function getOwnerName(): string {
		return $this->pet->getPetOwnerName();
	}
}