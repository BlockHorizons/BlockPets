<?php

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\HoveringPet;

class WitherSkullPet extends HoveringPet {

	public $height = 0.4;
	public $width = 0.4;

	public $name = "Wither Skull Pet";

	public $networkId = 89;

	protected $flyHeight = 13;
	protected $tier = self::TIER_UNCOMMON;
}