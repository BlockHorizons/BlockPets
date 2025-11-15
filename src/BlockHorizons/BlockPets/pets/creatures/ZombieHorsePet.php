<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombieHorsePet extends WalkingPet {

	public const NETWORK_NAME = "ZOMBIE_HORSE_PET";
	public const NETWORK_ORIG_ID = EntityIds::ZOMBIE_HORSE;

	protected string $name = "Zombie Horse Pet";

	protected float $width = 1.3965;
	protected float $height = 1.6;
}
