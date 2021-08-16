<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\inventory;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;

use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use muqsit\invmenu\inventory\InvMenuInventory;

use pocketmine\item\Item;
use pocketmine\nbt\tag\ListTag;
use pocketmine\Player;

class PetInventoryManager {

	public static function init(Loader $plugin): void {
		if(!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($plugin);
		}
	}

	/** @var InvMenu */
	private $menu;

	public function __construct(BasePet $pet) {
		$this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$this->setName($pet->getPetName());
	}

	public function setName(string $name): void {
		$this->menu->setName($name . "'s Inventory");
	}

	public function getInventory(): InvMenuInventory {
		return $this->menu->getInventory();
	}

	public function openAs(Player $player, ?int $forceId = null): void {
		$this->menu->send($player, $forceId);
	}

	public function read(ListTag $tag): void {
		$contents = [];
		foreach($tag->getAllValues() as $nbt) {
			$contents[$nbt->getByte("Slot")] = Item::nbtDeserialize($nbt);
		}

		$this->getInventory()->setContents($contents);
	}

	public function write(string $tag_name = ""): ListTag {
		$tag = new ListTag($tag_name);
		foreach($this->getInventory()->getContents() as $slot => $item) {
			$tag->push($item->nbtSerialize($slot));
		}

		return $tag;
	}
}

