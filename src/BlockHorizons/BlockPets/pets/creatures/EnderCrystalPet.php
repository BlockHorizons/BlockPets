<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EnderCrystalPet extends HoveringPet {

	const NETWORK_NAME = "ENDER_CRYSTAL_PET";
	const NETWORK_ORIG_ID = EntityIds::ENDER_CRYSTAL;

	protected float $width = 0.8;
	protected float $height = 0.8;

	protected string $name = "Ender Crystal Pet";
}