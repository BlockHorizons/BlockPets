<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class HorsePet extends WalkingPet {

	public const NETWORK_NAME    = "HORSE_PET";
	public const NETWORK_ORIG_ID = EntityIds::HORSE;

	public const TYPE_NONE        = 0;
	public const TYPE_WHITE       = 1;
	public const TYPE_WHITE_FIELD = 2;
	public const TYPE_WHITE_DOTS  = 3;
	public const TYPE_BLACK_DOTS  = 4;

	public const COLOR_WHITE = 0;
	public const COLOR_CREAMY = 1;
	public const COLOR_CHESTNUT = 2;
	public const COLOR_BROWN = 3;
	public const COLOR_BLACK = 4;
	public const COLOR_GRAY = 5;
	public const COLOR_DARKBROWN = 6;

	protected string $name = "Horse Pet";

	protected float $width = 1.3965;
	protected float $height = 1.6;

	protected int $variant;

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
		return $this->variant;
	}

	public function setVariant(int $type, int $colour): void {
		$this->variant = $colour | $type << 8;
		$this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $colour | $type << 8);
	}
}
