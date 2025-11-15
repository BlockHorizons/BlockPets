<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class SilverFishPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME = "SILVERFISH_PET";
	public const NETWORK_ORIG_ID = EntityIds::SILVERFISH;

	protected float $height = 0.3;
	protected float $width = 0.4;

	protected string $name = "SilverFish Pet";
}
