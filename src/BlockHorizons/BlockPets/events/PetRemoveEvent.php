<?php

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\Cancellable;

class PetRemoveEvent extends BlockPetsEvent implements Cancellable {

	public static $handlerList = null;

	private $pet;

	public function __construct(Loader $loader, BasePet $pet) {
		parent::__construct($loader);
		$this->pet = $pet;
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
	 * Returns the owner of the pet about to be spawned.
	 *
	 * @return string
	 */
	public function getPlayerName(): string {
		return $this->pet->getPetOwnerName();
	}
}