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
	private $countable;
	public function __construct(SignStatus $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->countable = 0;
	}

    public function onRun($currentTick){
    	$val = $this->plugin->enabled();;
		if($val === "true" || $val === true){
			$x = $this->plugin->getThisSignX();
			$y = $this->plugin->getThisSignY();
			$z = $this->plugin->getThisSignZ();
			$lvz = $this->plugin->level();
			$tps = Server::getInstance()->getTicksPerSecond();
			$p = count(Server::getInstance()->getOnlinePlayers());
			$full = Server::getInstance()->getMaxPlayers();
			$level = Server::getInstance()->getLevelByName($lvz);
			$sign = $level->getTile(new Vector3($x,$y,$z));
			$count = $this->countable++; //For debug
			if($sign instanceof Sign){
				$sign->setText("[STATUS]", "TPS: [$tps]", "ONLINE: $p/$full", "$count");
			}
		}
    }
}