<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class VexPet extends HoveringPet implements SmallCreature {

	const NETWORK_NAME = "VEX_PET";
	const NETWORK_ORIG_ID = EntityIds::VEX;

	protected float $height = 0.8;
	protected float $width = 0.4;

	protected string $name = "Vex Pet";

	public function generateCustomPetData(): void {
		$this->canCollide = false;
	}
}