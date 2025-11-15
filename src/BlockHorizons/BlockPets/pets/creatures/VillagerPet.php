<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\WalkingPet;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataProperties;
use Random\RandomException;

class VillagerPet extends WalkingPet {

	public const NETWORK_NAME = "VILLAGER_PET";
	public const NETWORK_ORIG_ID = EntityIds::VILLAGER;

	protected float $height = 1.95;
	protected float $width = 0.6;

	protected string $name = "Villager Pet";

	/**
	 * @throws RandomException
	 */
	public function generateCustomPetData(): void {
		$randomVariant = random_int(0, 5);
		$this->getNetworkProperties()->setInt(EntityMetadataProperties::VARIANT, $randomVariant);
	}
}
