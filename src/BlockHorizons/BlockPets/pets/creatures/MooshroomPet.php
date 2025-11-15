<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MooshroomPet extends WalkingPet {

	public const NETWORK_NAME = "MOOSHROOM_PET";
	public const NETWORK_ORIG_ID = EntityIds::MOOSHROOM;

	protected float $height = 1.4;
	protected float $width = 0.9;

	protected string $name = "Mooshroom Pet";
}
