<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class ElderGuardianPet extends SwimmingPet {

	public $width = 1.9975;
	public $height = 1.9975;

	public $name = "Elder Guardian Pet";
	public $networkId = 50;

	public function generateCustomPetData() {
		parent::generateCustomPetData();
		$this->setDataFlag(self::DATA_FLAGS, self::DATA_FLAG_ELDER, true);
	}
}