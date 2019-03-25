<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\nbt\tag\CompoundTag;

class HorsePet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "horse";
	protected const PET_NETWORK_ID = self::HORSE;

	public const TAG_VARIANT = "Variant";

	public const TYPE_NONE = 0;
	public const TYPE_WHITE = 1;
	public const TYPE_WHITE_FIELD = 2;
	public const TYPE_WHITE_DOTS = 3;
	public const TYPE_BLACK_DOTS = 4;

	public const HORSE_TYPES = [
		self::TYPE_NONE,
		self::TYPE_WHITE,
		self::TYPE_WHITE_FIELD,
		self::TYPE_WHITE_DOTS,
		self::TYPE_BLACK_DOTS
	];

	public const COLOR_WHITE = 0, COLOUR_WHITE = 0;
	public const COLOR_CREAMY = 1, COLOUR_CREAMY = 1;
	public const COLOR_CHESTNUT = 2, COLOUR_CHESTNUT = 2;
	public const COLOR_BROWN = 3, COLOUR_BROWN = 3;
	public const COLOR_BLACK = 4, COLOUR_BLACK = 4;
	public const COLOR_GRAY = 5, COLOUR_GRAY = 5;
	public const COLOR_DARKBROWN = 6, COLOUR_DARKBROWN = 6;

	public const HORSE_COLORS = [
		self::COLOR_WHITE,
		self::COLOR_CREAMY,
		self::COLOR_CHESTNUT,
		self::COLOR_BROWN,
		self::COLOR_BLACK,
		self::COLOR_GRAY,
		self::COLOR_DARKBROWN
	];

	public static function createVariant(int $type, int $colour): int {
		return $colour | $type << 8;
	}

	public $name = "Horse Pet";

	public $width = 1.3965;
	public $height = 1.6;

	protected function readPetData(CompoundTag $nbt): void {
		parent::readPetData($nbt);
		$this->setVariant($nbt->getShort(self::TAG_VARIANT, self::createVariant($this->getRandomType(), $this->getRandomColor())));
	}

	protected function writePetData(): CompoundTag {
		$nbt = parent::writePetData();
		$nbt->setShort(self::TAG_VARIANT, $this->getVariant());
		return $nbt;
	}

	public function getRandomType(): int {
		return array_rand(self::HORSE_TYPES);
	}

	public function getRandomColor(): int {
		return array_rand(self::HORSE_COLORS);
	}

	public function getVariant(): int {
		return $this->getDataPropertyManager()->getInt(self::DATA_VARIANT);
	}

	public function setVariant(int $variant): void {
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $variant);
	}
}
