<?php

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;

class BlockPetsConfig {

	private $loader;
	private $settings;

	public function __construct(Loader $loader) {
		$this->loader = $loader;

		$loader->saveDefaultConfig();
		$this->collectPreferences();
	}

	public function collectPreferences() {
		$data = yaml_parse_file($this->getLoader()->getDataFolder() . "config.yml");
		$this->settings = $data;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	/**
	 * @return bool
	 */
	public function arePetsInvulnerable(): bool {
		return (bool) $this->settings["Invulnerable-Pets"];
	}

	/**
	 * @return bool
	 */
	public function petsDoAttack(): bool {
		return (bool) $this->settings["Attacking-Pets"];
	}

	/**
	 * @return int
	 */
	public function getBasePetHealth(): int {
		return (int) $this->settings["Pet-Base-Health"];
	}

	/**
	 * @return float
	 */
	public function getPetHealthPerLevel(): float {
		return (float) $this->settings["Pet-Per-Level-Health"];
	}

	/**
	 * @return float
	 */
	public function getPetSizePerLevel(): float {
		return (float) $this->settings["Pet-Per-Level-Size"];
	}

	/**
	 * @return int
	 */
	public function getBasePetDamage(): int {
		return (int) $this->settings["Pet-Base-Damage"];
	}

	/**
	 * @return float
	 */
	public function getPetDamagePerLevel(): float {
		return (float) $this->settings["Pet-Per-Level-Damage"];
	}
}