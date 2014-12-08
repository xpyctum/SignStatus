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
	 public function __construct(SignStatus $plugin){
        parent::__construct($plugin);
        $this->plugin = $plugin;
    }

    public function onRun($currentTick){
    	$val = Server::getInstance()->getPluginManager()->getPlugin("SignStatus")->enabled();
		if($val === "true" || $val === true){
			$x = Server::getInstance()->getPluginManager()->getPlugin("SignStatus")->getThisSignX();
			$y = Server::getInstance()->getPluginManager()->getPlugin("SignStatus")->getThisSignY();
			$z = Server::getInstance()->getPluginManager()->getPlugin("SignStatus")->getThisSignZ();
			$lvz = $this->owner->getServer()->getPluginManager()->getPlugin("SignStatus")->level();
			$level = Server::getInstance()->getLevelByName($lvz);
			$sign = $level->getTile(new Vector3($x,$y,$z));
			if($sign instanceof Sign){
				$sign->setLine(1,"TPS");
				
			}
		}
    }
}