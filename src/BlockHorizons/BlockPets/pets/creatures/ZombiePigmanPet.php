<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombiePigmanPet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_PIGMAN_PET";
	const NETWORK_ORIG_ID = EntityIds::ZOMBIE_PIGMAN;

	protected float $height = 1.95;
	protected float $width = 0.6;

	protected string $name = "Zombie Pigman Pet";
}
