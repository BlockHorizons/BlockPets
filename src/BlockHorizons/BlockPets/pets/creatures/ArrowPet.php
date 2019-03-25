<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\nbt\tag\CompoundTag;

class ArrowPet extends HoveringPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "arrow";
	protected const PET_NETWORK_ID = self::ARROW;

	public const TAG_CRITICAL = "IsCritical";

	public $name = "Arrow Pet";

	public $width = 0.5;
	public $height = 0.5;

	public function isCritical(): bool {
		return $this->getGenericFlag(self::DATA_FLAG_CRITICAL);
	}

	public function setCritical(bool $value = true): void {
		$this->setGenericFlag(self::DATA_FLAG_CRITICAL, $value);
	}

	protected function readPetData(CompoundTag $nbt): void {
		parent::readPetData($nbt);
		$this->setCritical((bool) $nbt->getByte(self::TAG_CRITICAL, (int) $this->isCritical()));
	}

	protected function writePetData(): CompoundTag {
		$nbt = parent::writePetData();
		$nbt->setByte(self::TAG_CRITICAL, (int) $this->isCritical());
		return $nbt;
	}
}
