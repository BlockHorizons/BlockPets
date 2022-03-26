<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class GuardianPet extends SwimmingPet {

	const NETWORK_NAME = "GUARDIAN_PET";
	const NETWORK_ORIG_ID = EntityIds::GUARDIAN;

	protected float $width = 0.85;
	protected float $height = 0.85;

	protected string $name = "Guardian Pet";
}