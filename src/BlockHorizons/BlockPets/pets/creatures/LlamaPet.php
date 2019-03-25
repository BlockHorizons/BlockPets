<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\nbt\tag\CompoundTag;

class LlamaPet extends WalkingPet {

	protected const PET_SAVE_ID = parent::PET_SAVE_ID . "llama";
	protected const PET_NETWORK_ID = self::LLAMA;

	public const TAG_VARIANT = "Variant";

	public const TYPE_CREAMY = 0;
	public const TYPE_WHITE = 1;
	public const TYPE_BROWN = 2;
	public const TYPE_GRAY = 3;

	public const LLAMA_TYPES = [
		self::TYPE_CREAMY,
		self::TYPE_WHITE,
		self::TYPE_BROWN,
		self::TYPE_GRAY
	];

	public static function createVariant(int $type): int {
		return $type;
	}

	public $height = 0.935;
	public $width = 0.45;

	public $name = "Llama Pet";

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
		return array_rand(self::LLAMA_TYPES);
	}

	public function getVariant(): int {
		return $this->getDataPropertyManager()->getInt(self::DATA_VARIANT);
	}

	public function setVariant(int $variant): void {
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $variant);
	}
}
