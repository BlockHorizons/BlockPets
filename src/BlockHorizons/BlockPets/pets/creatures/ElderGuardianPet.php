<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\entity\EntityIds;
use pocketmine\network\mcpe\protocol\types\entity\EntityMetadataFlags;
use pocketmine\player\Player;

class ElderGuardianPet extends SwimmingPet {

	const NETWORK_NAME = "ELDER_GUARDIAN_PET";
	const NETWORK_ORIG_ID = EntityIds::ELDER_GUARDIAN;

	protected float $width = 1.9975;
	protected float $height = 1.9975;

	public string $name = "Elder Guardian Pet";

	public function generateCustomPetData(): void {
		parent::generateCustomPetData();
		$this->getNetworkProperties()->setGenericFlag(EntityMetadataFlags::ELDER, true);
	}

	public function attack(EntityDamageEvent $source): void {
		if($source instanceof EntityDamageByEntityEvent) {
			$attacker = $source->getDamager();
			if($attacker instanceof Player && random_int(0, 1)) {
				$attacker->getNetworkSession()->sendDataPacket(LevelEventPacket::create(2006, 0, $attacker->getLocation()));
			}
		}
		parent::attack($source);
	}
}
