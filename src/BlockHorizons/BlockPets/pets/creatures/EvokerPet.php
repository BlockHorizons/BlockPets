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
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ACTION, (bool) $isCasting);
		$this->setDataFlag(self::DATA_FLAGS, 42, (bool) $isCasting);
		$this->setDataFlag(self::DATA_FLAGS, 43, (bool) $isCasting);
		$this->setDataFlag(self::DATA_FLAGS, 44, (bool) $isCasting);
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_LINGER, (bool) $isCasting);
	}
}