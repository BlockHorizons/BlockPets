<?php

namespace BlockHorizons\BlockPets;

use pocketmine\scheduler\PluginTask;

abstract class BaseTask extends PluginTask {

	protected $loader;

	public function __construct(Loader $loader) {
		parent::__construct($loader);
		$this->loader = $loader;
	}

	/**
	 * @return Loader
	 */
	public function getLoader(): Loader {
		return $this->loader;
	}
}