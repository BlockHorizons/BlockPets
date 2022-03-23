<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\player\Player;

class PetSpawnEvent extends PetEvent implements Cancellable {

	use CancellableTrait;

	/**
	 * Returns the owner of the pet about to be spawned.
	 */
	public function getPlayer(): Player {
		return $this->pet->getPetOwner();
	}
}