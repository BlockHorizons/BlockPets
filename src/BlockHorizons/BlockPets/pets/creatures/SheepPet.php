<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\creatures\utils\EntityColorMeta;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\nbt\tag\CompoundTag;

class SheepPet extends WalkingPet implements EntityColorMeta {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "sheep";
	protected const PET_NETWORK_ID = self::SHEEP;

	public const TAG_COLOR = "Color";

	public $height = 1.3;
	public $width = 0.9;

	public $name = "Sheep Pet";

	public function getColor(): int {
		return $this->propertyManager->getByte(self::DATA_COLOR);
	}

	public function setColor(int $color): void {
		$this->propertyManager->setByte(self::DATA_COLOR, $color & 0x0f);
	}

	protected function readPetData(CompoundTag $nbt): void {
		parent::readPetData($nbt);
		$this->setColor($nbt->getShort(self::TAG_COLOR, $this->getRandomColor()));
	}

	protected function writePetData(): CompoundTag {
		$nbt = parent::writePetData();
		$nbt->setShort(self::TAG_COLOR, $this->getColor());
		return $nbt;
	}

	public function getRandomColor(): int {
		return array_rand(self::ALL_COLORS);
	}

	public function doPetUpdates(int $tickDiff): bool {
		if($this->ticksLived % 10 === 0 && $this->getPetName() === "jeb_") {
			$this->setColor($this->getColor() + 1);
		}
		return parent::doPetUpdates($tickDiff);
	}
}
