<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\inventory;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\BasePet;
use muqsit\invmenu\InvMenu;
use muqsit\invmenu\InvMenuHandler;
use pocketmine\inventory\Inventory;
use pocketmine\item\Item;
use pocketmine\nbt\BigEndianNbtSerializer;
use pocketmine\nbt\NBT;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\TreeRoot;
use pocketmine\player\Player;

class PetInventoryManager {

	/** @var BigEndianNbtSerializer */
	private static $nbtSerializer;

	public static function init(Loader $plugin): void {
		self::$nbtSerializer = new BigEndianNbtSerializer();
		if(!InvMenuHandler::isRegistered()) {
			InvMenuHandler::register($plugin);
		}
	}

	private InvMenu $menu;

	public function __construct(private BasePet $pet) {
		$this->menu = InvMenu::create(InvMenu::TYPE_CHEST);
		$this->menu->setInventoryCloseListener(function(): void {
			$pet = $this->getPet();
			$loader = $pet->getLoader();
			if($loader->getBlockPetsConfig()->storeToDatabase()) {
				$loader->getDatabase()->updateInventory($pet);
			}
		});
		$this->setName($pet->getPetName());
	}

	public function setName(string $name): void {
		$this->menu->setName($name . "'s Inventory");
	}

	public function getPet(): BasePet {
		return $this->pet;
	}

	public function getInventory(): Inventory {
		return $this->menu->getInventory();
	}

	public function openAs(Player $player, ?int $forceId = null): void {
		$this->menu->send($player, $forceId);
	}

	public function load(string $compressed): void {
		$contents = [];

		/** @var CompoundTag $nbt */
		foreach(self::$nbtSerializer->read($compressed)->mustGetCompoundTag()->getListTag("Inventory") as $nbt) {
			$contents[$nbt->getByte("Slot")] = Item::nbtDeserialize($nbt);
		}

		$this->getInventory()->setContents($contents);
	}

	public function compressContents(): string {
		$contents = [];

		foreach($this->getInventory()->getContents() as $slot => $item) {
			$contents[] = $item->nbtSerialize($slot);
		}

		$tag = CompoundTag::create()->setTag("Inventory", new ListTag($contents, NBT::TAG_Compound));

		return self::$nbtSerializer->write(new TreeRoot($tag));
	}
}