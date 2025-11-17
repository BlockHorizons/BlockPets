<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class StrayPet extends WalkingPet {

	public const NETWORK_NAME = "STRAY_PET";
	public const NETWORK_ORIG_ID = EntityIds::STRAY;

	protected float $height = 1.99;
	protected float $width = 0.6;

	protected string $name = "Stray Pet";
}
