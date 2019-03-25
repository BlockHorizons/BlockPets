<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\creatures\utils\EntityColorMeta;
use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\nbt\tag\CompoundTag;

class WolfPet extends WalkingPet implements SmallCreature, EntityColorMeta {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "wolf";
	protected const PET_NETWORK_ID = self::WOLF;

	public const TAG_COLOR = "Color";

	public $name = "Wolf Pet";

	public $width = 0.6;
	public $height = 0.85;

	public function generateCustomPetData(): void {
		$eid = 123456789123456789;
		$this->getDataPropertyManager()->setLong(self::DATA_OWNER_EID, $eid);
	}

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
}
