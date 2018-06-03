<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\inventory;

use muqsit\invmenu\inventories\ChestInventory;

use pocketmine\Player;

class PetInventory extends ChestInventory {

	/** @var PetInventoryManager */
	private $manager;

	public function setManager(PetInventoryManager $manager): void {
		$this->manager = $manager;
	}

	public function onClose(Player $player): void {
		parent::onClose($player);
		$pet = $this->manager->getPet();
		$loader = $pet->getLoader();
		if($loader->getBlockPetsConfig()->storeToDatabase()) {
			$loader->getDatabase()->updateInventory($pet);
		}
	}
}
