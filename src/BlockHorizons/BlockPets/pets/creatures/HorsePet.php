<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;

class HorsePet extends WalkingPet {

	const NETWORK_ID = self::HORSE;

	const TYPE_NONE = 0;
	const TYPE_WHITE = 1;
	const TYPE_WHITE_FIELD = 2;
	const TYPE_WHITE_DOTS = 3;
	const TYPE_BLACK_DOTS = 4;

	const COLOR_WHITE = 0, COLOUR_WHITE = 0;
	const COLOR_CREAMY = 1, COLOUR_CREAMY = 1;
	const COLOR_CHESTNUT = 2, COLOUR_CHESTNUT = 2;
	const COLOR_BROWN = 3, COLOUR_BROWN = 3;
	const COLOR_BLACK = 4, COLOUR_BLACK = 4;
	const COLOR_GRAY = 5, COLOUR_GRAY = 5;
	const COLOR_DARKBROWN = 6, COLOUR_DARKBROWN = 6;

	public $name = "Horse Pet";

	public $width = 1.4;
	public $height = 1.6;

	public function generateCustomPetData(): void {
		$this->setVariant($this->getRandomType(), $this->getRandomColor());
	}

	public function getRandomType(): int {
		return array_rand([
			self::TYPE_NONE,
			self::TYPE_WHITE,
			self::TYPE_WHITE_FIELD,
			self::TYPE_WHITE_DOTS,
			self::TYPE_BLACK_DOTS
		]);
	}

	public function getRandomColor(): int {
		return array_rand([
			self::COLOR_WHITE,
			self::COLOR_CREAMY,
			self::COLOR_CHESTNUT,
			self::COLOR_BROWN,
			self::COLOR_BLACK,
			self::COLOR_GRAY,
			self::COLOR_DARKBROWN
		]);
	}

	public function getVariant(): int {
		return $this->getDataPropertyManager()->getInt(self::DATA_VARIANT);
	}

	public function setVariant(int $type, int $colour): void {
		$this->getDataPropertyManager()->setInt(self::DATA_VARIANT, $colour | $type << 8);
	}
}