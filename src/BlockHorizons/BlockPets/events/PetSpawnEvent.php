<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use pocketmine\event\Cancellable;
use pocketmine\Player;

class PetSpawnEvent extends PetEvent implements Cancellable {

	/**
	 * Returns the owner of the pet about to be spawned.
	 *
	 * @return Player
	 */
	public function getPlayer(): Player {
		return $this->pet->getPetOwner();
	}
}