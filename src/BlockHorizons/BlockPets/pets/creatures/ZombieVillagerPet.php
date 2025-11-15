<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class ZombieVillagerPet extends WalkingPet {

	public const NETWORK_NAME = "ZOMBIE_VILLAGER_PET";
	public const NETWORK_ORIG_ID = EntityIds::ZOMBIE_VILLAGER;

	protected float $height = 1.95;
	protected float $width = 0.6;

	protected string $name = "Zombie Villager Pet";
}
