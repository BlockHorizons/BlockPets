<?php

namespace BlockHorizons\BlockPets\pets\inventory;

use BlockHorizons\BlockPets\pets\BasePet;
use pocketmine\block\Block;
use pocketmine\item\Item;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use pocketmine\utils\TextFormat;

class PetInventoryHolder {

	/*
	 * Most of the code written in this file comes from @Muqsit.
	 * Make sure to look at his PlayerVaults plugin too.
	 */

	private $pet;
	/** @var Item[] */
	private $items = [];

	public function __construct(BasePet $pet) {
		$this->pet = $pet;
		$this->items = $pet->getLoader()->getDatabase()->getInventory($pet->getPetName(), $pet->getPetOwnerName());
	}

	/**
	 * @return BasePet
	 */
	public function getPet(): BasePet {
		return $this->pet;
	}

	/**
	 * @return array
	 */
	public function getInventoryContents(): array {
		return $this->items;
	}

	/**
	 * @param array $contents
	 */
	public function setInventoryContents(array $contents) {
		$this->items = $contents;
	}

	/**
	 * @return bool
	 */
	public function openToOwner(): bool {
		$owner = $this->pet->getPetOwner();
		if($owner === null) {
			return false;
		}
		if(!($inventory = $this->deployToOwner())) {
			return false;
		}

		$inventory->setContents($this->items);
		$owner->addWindow($inventory);
		return true;
	}

	/**
	 * @return PetInventory|bool
	 */
	public function deployToOwner() {
		$owner = $this->pet->getPetOwner();
		if($owner === null) {
			return false;
		}
		/** @var Chest $tile */
		$tile = Tile::createTile(Tile::CHEST, $owner->level, new CompoundTag("", [
			new StringTag("id", Tile::CHEST),
			new StringTag("CustomName", ($this->pet->getPetName() . TextFormat::RESET . TextFormat::GREEN . "'s Inventory")),
			new IntTag("x", (int) $owner->x),
			new IntTag("y", (int) ($owner->y - 2)),
			new IntTag("z", (int) $owner->z)
		]));
		$this->chestPos = $tile->level->getBlock($tile);

		$block = Block::get(Block::CHEST);
		$block->setComponents((int) $tile->x, (int) $tile->y, (int) $tile->z);
		$block->level = $tile->level;
		$tile->level->sendBlocks([$owner], [$block]);
		$tile->spawnTo($owner);
		$inventory = new PetInventory($tile, $this->pet);
		$inventory->setContents($this->items);
		return $inventory;
	}

	/**
	 * @return string
	 */
	public function compressContents(): string {
		$items = $this->items;
		foreach($items as &$item) {
			$item = $item->nbtSerialize(-1, "Item");
		}
		$nbt = new NBT(NBT::BIG_ENDIAN);
		$compressedContents = new CompoundTag("Contents", [
			new ListTag("ItemList", (array) $items)
		]);
		$nbt->setData($compressedContents);
		return base64_encode($nbt->writeCompressed(ZLIB_ENCODING_DEFLATE));
	}
}