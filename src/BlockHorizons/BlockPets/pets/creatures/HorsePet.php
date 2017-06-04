<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\entity\Attribute;
use pocketmine\network\mcpe\protocol\UpdateAttributesPacket;
use pocketmine\Player;

class HorsePet extends WalkingPet {

	public $speed = 1.8;
	public $networkId = 23;

	public $name = "Horse Pet";
	public $tier = self::TIER_LEGENDARY;

	public $width = 1.4;
	public $height = 1.6;

	public function recalculateAttributes(Player $player) {
		$entry = [];
		$entry[] = new Attribute($this->getId(), "minecraft:horse.jump_strength", 0, 3, 0.6679779);
		$entry[] = new Attribute($this->getId(), "minecraft:fall_damage", 0, 3.402823, 1);
		$entry[] = new Attribute($this->getId(), "minecraft:luck", -1024, 1024, 0);
		$entry[] = new Attribute($this->getId(), "minecraft:movement", 0, 3.402823, 0.223);
		$entry[] = new Attribute($this->getId(), "minecraft:absorption", 0, 3.402823, 0);
		$entry[] = new Attribute($this->getId(), "minecraft:health", 0, 40, 40);

		$pk = new UpdateAttributesPacket();
		$pk->entries = $entry;
		$pk->entityId = $this->getId();
		$player->dataPacket($pk);
	}
}