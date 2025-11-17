<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use Random\RandomException;

class WolfPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME = "WOLF_PET";
	public const NETWORK_ORIG_ID = EntityIds::WOLF;

	protected string $name = "Wolf Pet";

	protected float $width = 0.6;
	protected float $height = 0.85;

	/**
	 * @throws RandomException
	 */
	public function generateCustomPetData(): void {
		$randomColour = random_int(0, 15);
		$eid = 123456789123456789;
		$this->getNetworkProperties()->setLong(EntityMetadataProperties::OWNER_EID, $eid);
		$this->getNetworkProperties()->setByte(EntityMetadataProperties::COLOR, $randomColour);
	}
}
