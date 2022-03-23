<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\items;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;

class Saddle extends Item {

	public function __construct(int $meta = 0) {
		parent::__construct(new ItemIdentifier(ItemIds::SADDLE, $meta), "Saddle");
	}
}