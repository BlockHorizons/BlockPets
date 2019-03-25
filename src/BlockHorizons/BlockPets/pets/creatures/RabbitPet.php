<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\nbt\tag\CompoundTag;

class RabbitPet extends BouncingPet implements SmallCreature {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "rabbit";
	protected const PET_NETWORK_ID = self::RABBIT;

	public const TAG_VARIANT = "Variant";

	public const TYPE_BROWN = 0;
	public const TYPE_WHITE = 1;
	public const TYPE_BLACK = 2;
	public const TYPE_BLACK_AND_WHITE = 3;
	public const TYPE_GOLD = 4;
	public const TYPE_SALT_AND_PEPPER = 5;
	public const TYPE_KILLER_BUNNY = 6;

	public const RABBIT_TYPES = [
		self::TYPE_BROWN,
		self::TYPE_WHITE,
		self::TYPE_BLACK,
		self::TYPE_BLACK_AND_WHITE,
		self::TYPE_GOLD,
		self::TYPE_SALT_AND_PEPPER,
		self::TYPE_KILLER_BUNNY
	];

	public static function createVariant(int $type): int {
		return $type;
	}

	public $height = 0.5;
	public $width = 0.4;

	public $name = "Rabbit Pet";

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
		return array_rand(self::RABBIT_TYPES);
	}

	public function getVariant(): int {
		return $this->getDataPropertyManager()->getInt(self::DATA_VARIANT);
	}

	public function setVariant(int $variant): void {
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $variant);
	}
}
