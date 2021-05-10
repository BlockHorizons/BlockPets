<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class SheepPet extends WalkingPet {

	const NETWORK_NAME = "SHEEP_PET";
	const NETWORK_ORIG_ID = self::SHEEP;

	public $height = 1.3;
	public $width = 0.9;

	public $name = "Sheep Pet";

	public function generateCustomPetData(): void {
		$this->setColor(random_int(0, 15));
	}

	public function setColor(int $color): void {
		$this->propertyManager->setByte(self::DATA_COLOUR, $color % 16);
	}

	public function getColor(): int {
		return $this->propertyManager->getByte(self::DATA_COLOR);
	}

	public function doPetUpdates(int $currentTick): bool {
		if($currentTick % 10 === 0 && $this->getPetName() === "jeb_") {
			$this->setColor($this->getColor() + 1);
		}
		return parent::doPetUpdates($currentTick);
	}
}
