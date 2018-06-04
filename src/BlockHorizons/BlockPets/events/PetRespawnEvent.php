<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\Cancellable;

class PetRespawnEvent extends PetEvent implements Cancellable {

	/** @var int */
	private $delay = 0;

	public function __construct(Loader $loader, BasePet $pet, int $delay) {
		parent::__construct($loader, $pet);
		$this->delay = $delay;
	}

	/**
	 * Returns the delay in seconds for the pet to respawn.
	 *
	 * @return int
	 */
	public function getDelay(): int {
		return $this->delay;
	}

	/**
	 * Sets the delay for a pet to respawn.
	 *
	 * @param int $secondsDelay
	 */
	public function setDelay(int $secondsDelay): void {
		$this->delay = $secondsDelay;
	}
}