<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;

class PetRespawnEvent extends PetEvent implements Cancellable {

	use CancellableTrait;

	public function __construct(Loader $loader, BasePet $pet, private int $delay) {
		parent::__construct($loader, $pet);
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