<?php
declare(strict_types = 1);

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;

class BlockPetsConfig {

	private array $settings;

	public function __construct(private Loader $loader) {
		$loader->saveDefaultConfig();
		$this->collectPreferences();
	}

	public function collectPreferences(): void {
		$data = yaml_parse_file($this->getLoader()->getDataFolder() . "config.yml");
		$this->settings = $data;
	}

	public function getLoader(): Loader {
		return $this->loader;
	}

	public function doHardReset(): bool {
		return (bool) ($this->settings["Hard-Reset"] ?? false);
	}

	public function arePetsInvulnerable(): bool {
		return (bool) ($this->settings["Invulnerable-Pets"] ?? false);
	}

	public function petsDoAttack(): bool {
		return (bool) ($this->settings["Attacking-Pets"] ?? true);
	}

	public function getBasePetHealth(): int {
		return (int) ($this->settings["Pet-Base-Health"] ?? 20);
	}

	public function getPetHealthPerLevel(): float {
		return (float) ($this->settings["Pet-Per-Level-Health"] ?? 0.5);
	}

	public function getPetSizePerLevel(): float {
		return (float) ($this->settings["Pet-Per-Level-Size"] ?? 0.03);
	}

	public function getBasePetDamage(): int {
		return (int) ($this->settings["Pet-Base-Damage"] ?? 4);
	}

	public function getPetDamagePerLevel(): float {
		return (float) ($this->settings["Pet-Per-Level-Damage"] ?? 0.1);
	}

	public function getRespawnTime(): int {
		return (int) ($this->settings["Pet-Respawn-Time"] ?? 10);
	}

	public function getPlayerExperiencePoints(): int {
		return (int) ($this->settings["Experience-Points-Per-Player-Kill"] ?? 15);
	}

	public function getEntityExperiencePoints(): int {
		return (int) ($this->settings["Experience-Points-Per-Entity-Kill"] ?? 10);
	}

	public function getMySQLInfo(): array {
		return (array) ($this->settings["MySQL-Info"] ?? []);
	}

	public function storeToDatabase(): bool {
		return (bool) ($this->settings["Store-To-Database"] ?? true);
	}

	public function getDatabase(): string {
		return (string) ($this->settings["Database"] ?? "SQLite");
	}

	public function getLanguage(): string {
		return (string) ($this->settings["Language"] ?? "en");
	}

	public function getMaxPets(): int {
		return (int) ($this->settings["Pet-Limit"] ?? 3);
	}

	public function arePetsInvulnerableIfOwnerIs(): bool {
		return (bool) ($this->settings["Invulnerable-If-Owner-Is"] ?? true);
	}

	public function giveExperienceWhenFed(): bool {
		return (bool) ($this->settings["Give-Experience-For-Feeding"] ?? true);
	}

	public function shouldStalkPetOwner(): bool {
		return (bool) ($this->settings["Stalk-Owner-Blindly"] ?? false);
	}
}
