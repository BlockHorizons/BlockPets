<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class CowPet extends WalkingPet {

	public const NETWORK_NAME = "COW_PET";
	public const NETWORK_ORIG_ID = EntityIds::COW;

	protected string $name = "Cow Pet";

	protected float $height = 1.4;
	protected float $width = 0.9;
}
