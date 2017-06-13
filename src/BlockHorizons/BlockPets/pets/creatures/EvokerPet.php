<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EvokerPet extends WalkingPet {

	public $networkId = 104;
	public $name = "Evoker Pet";
	public $tier = self::TIER_SPECIAL;
	public $speed = 1.4;

	public $width = 0.6;
	public $height = 1.95;

	public function generateCustomPetData() {
		$isCasting = mt_rand(0, 1);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_EVOKER_SPELL, (bool) $isCasting);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CHARGE_ATTACK, (bool) $isCasting);
		$this->setDataProperty(self::DATA_POTION_COLOR, self::DATA_TYPE_INT, 0xff000000 | (206 << 16) | (201 << 8) | 92);
		$this->setDataProperty(self::DATA_POTION_AMBIENT, self::DATA_TYPE_BYTE, 1);
	}
}