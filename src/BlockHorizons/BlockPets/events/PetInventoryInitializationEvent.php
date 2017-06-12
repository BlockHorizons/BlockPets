<?php

namespace BlockHorizons\BlockPets\events;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\event\Cancellable;

class PetInventoryInitializationEvent extends BlockPetsEvent implements Cancellable {

	public static $handlerList = null;

	private $pet;

	public function __construct(Loader $loader, BasePet $pet) {
		parent::__construct($loader);
		$this->pet = $pet;
	}

	/**
	 * Returns the pet whom's inventory is about to be intialized.
	 *
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}

	/**
	 * Returns the name of the owner of the pet.
	 *
	 * @return string
	 */
	public function getOwnerName(): string {
		return $this->pet->getPetOwnerName();
	}
}