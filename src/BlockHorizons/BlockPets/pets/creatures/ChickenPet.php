<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ChickenPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME = "CHICKEN_PET";
	public const NETWORK_ORIG_ID = EntityIds::CHICKEN;

	protected float $width = 0.4;
	protected float $height = 0.7;

	protected string $name = "Chicken Pet";
}