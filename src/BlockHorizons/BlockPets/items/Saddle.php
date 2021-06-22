<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\items;

use pocketmine\item\Item;

class Saddle extends Item {

	public function __construct(int $meta = 0) {
		parent::__construct(self::SADDLE, $meta, "Saddle");
	}

	public function getMaxStackSize(): int {
		return 1;
	}

}