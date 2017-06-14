<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class ElderGuardianPet extends SwimmingPet {

	public $width = 1.9975;
	public $height = 1.9975;

	public $name = "Elder Guardian Pet";
	public $swimmingSpeed = 2.0;

	public $networkId = 50;

	protected $tier = self::TIER_LEGENDARY;

	public function generateCustomPetData() {
		parent::generateCustomPetData();
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ELDER, true);
	}
}