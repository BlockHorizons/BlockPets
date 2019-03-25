<?php

declare(strict_types = 1);

namespace BlockHorizons\BlockPets\configurable;

use BlockHorizons\BlockPets\Loader;
use BlockHorizons\BlockPets\pets\utils\ExponentialLevelCurve;
use BlockHorizons\BlockPets\pets\utils\LevelCalculator;
use BlockHorizons\BlockPets\pets\utils\LinearLevelCurve;

class ExperienceConfig {

	private $loader;
	private $settings;

	public function __construct(Loader $loader) {
		$this->loader = $loader;
		$loader->saveResource("experience.yml");
		$this->collectPreferences();
		$this->updateLevelCalculator();
	}

	public function collectPreferences(): void {
		$data = yaml_parse_file($this->getLoader()->getDataFolder() . "experience.yml");
		$this->settings = $data;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}

	public function getCurve(): string {
		return strtolower((string) $this->settings["Curve"] ?? "linear");
	}

	public function getFormula(): array {
		return (array) $this->settings[ucfirst($this->getCurve())];
	}

	public function updateLevelCalculator(): void {
		$formula = $this->getFormula();
		switch($this->getCurve()) {
			case "linear":
				$curve = new LinearLevelCurve($formula["base"], $formula["multiplier"]);
				break;
			case "exponential":
				$curve = new ExponentialLevelCurve($formula["base"], $formula["multiplier"], $formula["exponent"]);
				break;
			default:
				throw new \InvalidArgumentException("Got invalid value for experience curve: " . $this->getCurve());
		}

		LevelCalculator::set($curve);
	}
}