<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EvokerPet extends WalkingPet {

	const NETWORK_ID = self::EVOCATION_ILLAGER;

	public $name = "Evoker Pet";

	public $width = 0.6;
	public $height = 1.95;

	public function generateCustomPetData(): void {
		$isCasting = random_int(0, 1);
		$this->setGenericFlag(self::DATA_FLAG_EVOKER_SPELL, (bool) $isCasting);
	}
}