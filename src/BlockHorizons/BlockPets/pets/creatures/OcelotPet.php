<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SmallCreature;
use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class OcelotPet extends WalkingPet implements SmallCreature {

	public const NETWORK_NAME = "OCELOT_PET";
	public const NETWORK_ORIG_ID = EntityIds::OCELOT;

	protected string $name = "Ocelot Pet";

	protected float $width = 0.6;
	protected float $height = 0.7;

	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 3);
		$this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
	}
}
