<?php

namespace BlockHorizons\BlockPets\pets\inventory;

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
		$this->tile = $tile;
	}

	/**
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}

	public function onClose(Player $player) {
		$this->save();
		$player->level->sendBlocks([$player], [$this->chestPos]);
		$this->tile->close();
	}

	public function save(): bool {
		if(!empty($this->getContents())) {
			$this->pet->getInventory()->setInventoryContents($this->getContents());
			$this->pet->getLoader()->getDatabase()->updateInventory($this->pet->getPetName(), $this->pet->getPetOwnerName(), $this->pet->getInventory()->compressContents());
		}
		return true;
	}
}