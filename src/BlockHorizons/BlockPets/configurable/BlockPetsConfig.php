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

	/**
	 * @return int
	 */
	public function getRespawnTime(): int {
		return (int) $this->settings["Pet-Respawn-Time"];
	}

	/**
	 * @return int
	 */
	public function getPlayerExperiencePoints(): int {
		return (int) $this->settings["Experience-Points-Per-Player-Kill"];
	}

	/**
	 * @return int
	 */
	public function getEntityExperiencePoints(): int {
		return (int) $this->settings["Experience-Points-Per-Entity-Kill"];
	}

	/**
	 * @return array
	 */
	public function getMySQLInfo(): array {
		return (array) $this->settings["MySQL-Info"];
	}

	/**
	 * @return bool
	 */
	public function storeToDatabase(): bool {
		return (bool) $this->settings["Store-To-Database"];
	}

	/**
	 * @return bool
	 */
	public function fetchFromDatabase(): bool {
		return (bool) $this->settings["Fetch-From-Database"];
	}

	/**
	 * @return string
	 */
	public function getDatabase(): string {
		return (string) $this->settings["Database"];
	}

	/**
	 * @return int
	 */
	public function getMaxPets(): int {
		return (int) $this->settings["Pet-Limit"];
	}

	/**
	 * @return bool
	 */
	public function arePetsInvulnerableIfOwnerIs(): bool {
		return (bool) $this->settings["Invulnerable-If-Owner-Is"];
	}

	/**
	 * @return bool
	 */
	public function giveExperienceWhenFed(): bool {
		return (bool) $this->settings["Give-Experience-For-Feeding"];
	}
}