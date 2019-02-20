<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\datastorage\types;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\inventory\PetInventory;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;

use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;

class PetInventoryManager {

	/** @var BigEndianNBTStream */
	private static $nbtParser;

	public static function init(Loader $plugin): void {
		self::$nbtParser = new BigEndianNBTStream();
		if(!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($plugin);
		}
	}

	/** @var InvMenu */
	private $menu;
	/** @var PetData */
	private $pet_data;

	public function __construct(PetData $pet_data) {
		$this->pet_data = $pet_data;
		$this->menu = InvMenu::create(PetInventory::class);
		$this->setName($pet_data->getName());
	}

	public function setName(string $name): void {
		$this->menu->setName($name . "'s Inventory");
	}

	public function getPetData(): PetData {
		return $this->pet_data;
	}

	public function getInventory(): PetInventory {
		return $this->menu->getInventory();
	}

	public function openAs(Player $player, ?int $forceId = null): void {
		$this->menu->send($player, $forceId);
	}

	public function read(string $compressed): void {
		$contents = [];
		foreach(self::$nbtParser->readCompressed($compressed)->getListTag("Inventory") as $nbt) {
			$contents[$nbt->getByte("Slot")] = Item::nbtDeserialize($nbt);
		}

		$this->getInventory()->setContents($contents);
	}

	public function write(): string {
		$list = new ListTag("Inventory");
		foreach($this->getInventory()->getContents() as $slot => $item) {
			$list->push($item->nbtSerialize($slot));
		}

		$tag = new CompoundTag();
		$tag->setTag($list);
		return self::$nbtParser->writeCompressed($tag);
	}
}

