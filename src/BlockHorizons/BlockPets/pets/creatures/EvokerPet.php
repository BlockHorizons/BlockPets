<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EvokerPet extends WalkingPet {

	public $networkId = 104;
	public $name = "Evoker Pet";
	public $tier = self::TIER_SPECIAL;

	public $width = 0.6;
	public $height = 1.95;

	public function generateCustomPetData() {
		$isCasting = mt_rand(0, 1);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_EVOKER_SPELL, (bool) $isCasting);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_CHARGE_ATTACK, (bool) $isCasting);
	}
}