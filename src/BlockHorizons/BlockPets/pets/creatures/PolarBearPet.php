<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PolarBearPet extends WalkingPet {

	public const NETWORK_NAME = "POLAR_BEAR_PET";
	public const NETWORK_ORIG_ID = EntityIds::POLAR_BEAR;

	protected float $height = 1.4;
	protected float $width = 1.3;

	protected string $name = "Polar Bear Pet";
}