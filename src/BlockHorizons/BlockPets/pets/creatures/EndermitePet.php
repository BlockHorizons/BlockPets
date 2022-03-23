<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;

class EndermitePet extends WalkingPet implements SmallCreature {

	const NETWORK_NAME = "ENDERMITE_PET";
	const NETWORK_ORIG_ID = EntityIds::ENDERMITE;

	protected float $height = 0.3;
	protected float $width = 0.4;

	protected string $name = "Endermite Pet";
}
