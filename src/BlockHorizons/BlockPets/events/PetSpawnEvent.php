<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use pocketmine\player\Player;

class PetSpawnEvent extends PetEvent {
	/**
	 * Returns the owner of the pet about to be spawned.
	 */
	public function getPlayer(): Player {
		return $this->pet->getPetOwner();
	}
}