<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\Cancellable;

class PetRespawnEvent extends BlockPetsEvent implements Cancellable {

	public static $handlerList = null;

	private $delay = 0;
	private $pet;

	public function __construct(Loader $loader, BasePet $pet, int $delay) {
		parent::__construct($loader);
		$this->pet = $pet;
		$this->delay = $delay;
	}

	/**
	 * Returns the pet that will be respawned.
	 *
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
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
	public function setDelay(int $secondsDelay) {
		$this->delay = $secondsDelay;
	}
}