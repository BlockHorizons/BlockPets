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

		if($this->doHardReset()) {
			$this->getLoader()->getConfig()->set("Fetch-From-Database", true);
			$this->settings["Fetch-From-Database"] = true;
		}
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
	public function doHardReset(): bool {
		return (bool) $this->settings["Hard-Reset"] ?? false;
	}

	/**
	 * @return bool
	 */
	public function arePetsInvulnerable(): bool {
		return (bool) $this->settings["Invulnerable-Pets"] ?? false;
	}

	/**
	 * @return bool
	 */
	public function petsDoAttack(): bool {
		return (bool) $this->settings["Attacking-Pets"] ?? true;
	}

	/**
	 * @return int
	 */
	public function getBasePetHealth(): int {
		return (int) $this->settings["Pet-Base-Health"] ?? 20;
	}

	/**
	 * @return float
	 */
	public function getPetHealthPerLevel(): float {
		return (float) $this->settings["Pet-Per-Level-Health"] ?? 0.5;
	}

	/**
	 * @return float
	 */
	public function getPetSizePerLevel(): float {
		return (float) $this->settings["Pet-Per-Level-Size"] ?? 0.03;
	}

	/**
	 * @return int
	 */
	public function getBasePetDamage(): int {
		return (int) $this->settings["Pet-Base-Damage"] ?? 4;
	}

	/**
	 * @return float
	 */
	public function getPetDamagePerLevel(): float {
		return (float) $this->settings["Pet-Per-Level-Damage"] ?? 0.1;
	}

	/**
	 * @return int
	 */
	public function getRespawnTime(): int {
		return (int) $this->settings["Pet-Respawn-Time"] ?? 10;
	}

	/**
	 * @return int
	 */
	public function getPlayerExperiencePoints(): int {
		return (int) $this->settings["Experience-Points-Per-Player-Kill"] ?? 15;
	}

	/**
	 * @return int
	 */
	public function getEntityExperiencePoints(): int {
		return (int) $this->settings["Experience-Points-Per-Entity-Kill"] ?? 10;
	}

	/**
	 * @return array
	 */
	public function getMySQLInfo(): array {
		return (array) $this->settings["MySQL-Info"] ?? [];
	}

	/**
	 * @return bool
	 */
	public function storeToDatabase(): bool {
		return (bool) $this->settings["Store-To-Database"] ?? true;
	}

	/**
	 * @return bool
	 */
	public function fetchFromDatabase(): bool {
		return (bool) $this->settings["Fetch-From-Database"] ?? false;
	}

	/**
	 * @return string
	 */
	public function getDatabase(): string {
		return (string) $this->settings["Database"] ?? "SQLite";
	}
	
	/**
	 * @return string
	 */
	public function getLanguage(): string {
	    return (string) $this->settings["Language"] ?? "en";
	}

	/**
	 * @return int
	 */
	public function getMaxPets(): int {
		return (int) $this->settings["Pet-Limit"] ?? 3;
	}

	/**
	 * @return bool
	 */
	public function arePetsInvulnerableIfOwnerIs(): bool {
		return (bool) $this->settings["Invulnerable-If-Owner-Is"] ?? true;
	}

	/**
	 * @return bool
	 */
	public function giveExperienceWhenFed(): bool {
		return (bool) $this->settings["Give-Experience-For-Feeding"] ?? true;
	}

	/**
	 * @return float
	 */
	public function getMaxPetSize(): float {
		return (float) $this->settings["Pet-Max-Size"] ?? 15.0;
	}

	/** 
	 * @return bool
	 */
	public function shouldStalkPetOwner(): bool {
		return (bool) $this->settings["Stalk-Owner-Blindly"] ?? false;
	}
}
