<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\Player;

class ElderGuardianPet extends SwimmingPet {

	const NETWORK_ID = self::ELDER_GUARDIAN;

	public $width = 1.9975;
	public $height = 1.9975;

	public $name = "Elder Guardian Pet";

	public function generateCustomPetData(): void {
		parent::generateCustomPetData();
		$this->setGenericFlag(self::DATA_FLAG_ELDER, true);
	}

	public function attack(EntityDamageEvent $source): void {
		if($source instanceof EntityDamageByEntityEvent) {
			$attacker = $source->getDamager();
			if($attacker instanceof Player && random_int(0, 1)) {
				$pk = new LevelEventPacket();
				$pk->evid = 2006;
				$pk->data = 0;
				$attacker->dataPacket($pk);
			}
		}
		parent::attack($source);
	}
}
