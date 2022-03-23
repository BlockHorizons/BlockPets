<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombiePet extends WalkingPet {

	const NETWORK_NAME = "ZOMBIE_PET";
	const NETWORK_ORIG_ID = EntityIds::ZOMBIE;

	protected string $name = "Zombie Pet";

	protected float $width = 0.6;
	protected float $height = 1.95;
}
