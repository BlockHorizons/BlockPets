<?php

namespace BlockHorizons\BlockPets\items;


use pocketmine\item\Item;

class Saddle extends Item{
	public function __construct(int $meta = 0){
		parent::__construct(self::SADDLE, $meta, "Saddle");
	}

}