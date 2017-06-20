<?php

namespace BlockHorizons\BlockPets\pets\datastorage;

use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\InventoryType;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;

class PetInventory extends ChestInventory {

	private $pet;
	private $chestPos = null;
	/** @var null|Tile */
	private $tile = null;

	public function __construct(Chest $tile, BasePet $pet) {
		parent::__construct($tile, InventoryType::get(InventoryType::CHEST));
		$this->pet = $pet;
	}

	/**
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}

	public function save(): bool {
		$this->pet->getInventory()->setInventoryContents($this->getContents());
		return true;
	}

	public function onClose(Player $player) {
		$player->level->sendBlocks([$player], [$this->chestPos]);
		$this->tile->close();
	}
}