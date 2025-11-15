<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class PigPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME    = "PIG_PET";
	public const NETWORK_ORIG_ID = EntityIds::PIG;

	protected float $height = 0.9;
	protected float $width = 0.9;

	protected string $name = "Pig Pet";
}
