<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\inventory;

use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\block\Block;
use pocketmine\inventory\ChestInventory;
use pocketmine\inventory\InventoryType;
use pocketmine\item\Item;
use pocketmine\Player;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;

class PetInventory extends ChestInventory {

	/** @var BasePet */
	private $pet;
	/** @var null|Block */
	private $chestPos = null;
	/** @var null|Tile */
	private $tile = null;

	public function __construct(Chest $tile, BasePet $pet) {
		parent::__construct($tile);
		$this->pet = $pet;
		$this->tile = $tile;
	}

	/**
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}

	public function onClose(Player $player): void {
		$this->save();
		$player->level->sendBlocks([$player], [$this->chestPos]);
		$this->tile->close();
	}

	public function save(): bool {
		$this->pet->getInventory()->setInventoryContents($this->getContents());
		$this->pet->getLoader()->getDatabase()->updateInventory($this->pet->getPetName(), $this->pet->getPetOwnerName(), $this->pet->getInventory()->compressContents());

		return true;
	}
}