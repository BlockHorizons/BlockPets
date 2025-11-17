<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;
use BlockHorizons\BlockPets\pets\SmallCreature;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;

class ArrowPet extends HoveringPet implements SmallCreature {

	public const NETWORK_NAME    = "ARROW_PET";
	public const NETWORK_ORIG_ID = EntityIds::ARROW;

	protected string $name = "Arrow Pet";

	protected float $width = 0.5;
	protected float $height = 0.5;

	public function setCritical(bool $value = true): void {
		$this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::CRITICAL, $value);
	}
}