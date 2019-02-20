<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class EvokerPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "evocation_illager";
	protected const PET_NETWORK_ID = self::EVOCATION_ILLAGER;

	public $name = "Evoker Pet";

	public $width = 0.6;
	public $height = 1.95;

	public function generateCustomPetData(): void {
		$isCasting = random_int(0, 1);
		$this->setGenericFlag(self::DATA_FLAG_EVOKER_SPELL, (bool) $isCasting);
	}
}
