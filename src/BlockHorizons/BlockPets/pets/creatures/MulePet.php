<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class MulePet extends WalkingPet {

	public const NETWORK_NAME = "MULE_PET";
	public const NETWORK_ORIG_ID = EntityIds::MULE;

	protected string $name = "Mule Pet";

	protected float $width = 1.3965;
	protected float $height = 1.6;
}
