<?php

namespace SignStatus;
use pocketmine\scheduler\PluginTask;
use pocketmine\Player; 
use pocketmine\Server; 
use pocketmine\level\Level; 
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\tile\Sign;

class Task extends PluginTask{
	private $plugin;
	public function __construct(SignStatus $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
	}

	public function onRun($currentTick){
		$val = $this->plugin->enabled();
		if($val === "true" || $val === true){
			$x = $this->plugin->getThisSignX();
			$y = $this->plugin->getThisSignY();
			$z = $this->plugin->getThisSignZ();
			$lvz = $this->plugin->level();
			$level = $this->plugin->getServer()->getLevelByName($lvz);
			$sign = $level->getTile(new Vector3($x,$y,$z));
			if($sign instanceof Sign){
				$texts = $sign->getText();
				$texts[0] = "TPS";
				$sign->setText(...$texts);
			}
		}
	}
}
