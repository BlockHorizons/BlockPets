<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\BouncingPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;

class RabbitPet extends BouncingPet implements SmallCreature {

	const NETWORK_NAME = "RABBIT_PET";
	const NETWORK_ORIG_ID = EntityIds::RABBIT;

	protected float $height = 0.5;
	protected float $width = 0.4;

	protected string $name = "Rabbit Pet";

	public function generateCustomPetData(): void {
		parent::generateCustomPetData();
		$variants = [
			0, 1, 2, 3, 4, 5, 99
		];
		$randomVariant = $variants[array_rand($variants)];
		$this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
	}
}