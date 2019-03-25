<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\nbt\tag\CompoundTag;

class OcelotPet extends WalkingPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "ocelot";
	protected const PET_NETWORK_ID = self::OCELOT;

	public const TAG_VARIANT = "Variant";

	public const TYPE_WILD = 0;
	public const TYPE_TUXEDO = 1;
	public const TYPE_TABBY = 2;
	public const TYPE_SIAMESE = 3;

	public const OCELOT_TYPES = [
		self::TYPE_WILD,
		self::TYPE_TUXEDO,
		self::TYPE_TABBY,
		self::TYPE_SIAMESE
	];

	public static function createVariant(int $type): int {
		return $type;
	}

	public $name = "Ocelot Pet";

	public $width = 0.6;
	public $height = 0.7;

	protected function readPetData(CompoundTag $nbt): void {
		parent::readPetData($nbt);
		$this->setVariant($nbt->getShort(self::TAG_VARIANT, self::createVariant($this->getRandomType())));
	}

	protected function writePetData(): CompoundTag {
		$nbt = parent::writePetData();
		$nbt->setShort(self::TAG_VARIANT, $this->getVariant());
		return $nbt;
	}

	public function getRandomType(): int {
		return array_rand(self::OCELOT_TYPES);
	}

	public function getVariant(): int {
		return $this->getDataPropertyManager()->getInt(self::DATA_VARIANT);
	}

	public function setVariant(int $variant): void {
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $variant);
	}
}
