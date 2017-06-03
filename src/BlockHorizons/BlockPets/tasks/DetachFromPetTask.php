<?php

namespace BlockHorizons\BlockPets\tasks;

use BlockHorizons\BlockPets\Loader;
use pocketmine\scheduler\PluginTask;

class DetachFromPetTask extends PluginTask {

	private $loader;

	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->loader = $loader;
	}

	public function onRun($currentTick) {
		foreach($this->getLoader()->getServer()->getOnlinePlayers() as $player) {
			if($this->getLoader()->isRidingAPet($player)) {
				if($player->distance($this->getLoader()->getRiddenPet($player)) > 2.5 + $this->getLoader()->getRiddenPet($player)->getScale()) {
					$this->getLoader()->getRiddenPet($player)->throwRiderOff();
				}
			}
		}
	}

	public function getLoader(): Loader {
		return $this->loader;
	}
}