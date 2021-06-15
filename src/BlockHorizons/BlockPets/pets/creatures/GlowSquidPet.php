<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\pets\creatures;

use BlockHorizons\BlockPets\pets\SwimmingPet;

class GlowSquidPet extends SwimmingPet {

	const NETWORK_NAME = "GLOW_SQUID_PET";
	const BLOCKPET_ENTITY_ID = "minecraft:glow_squid";

	public $height = 0.95;
	public $width = 0.95;

	public $name = "Glow Squid Pet";
}