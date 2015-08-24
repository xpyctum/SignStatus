<?php

namespace SignStatus;

use pocketmine\scheduler\PluginTask;
use pocketmine\Server; 
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat as F;

class Task extends PluginTask{
	private $plugin;
	private $countable;
	public function __construct(SignStatus $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->countable = 0;
	}

    	public function onRun($currentTick){
    		$val = $this->plugin->sign->get("sign")["enabled"];
		if($val === "true" || $val === true){
			foreach($this->plugin->getServer()->getLevels() as $levels){
            			foreach($levels->getTiles() as $tile){
                			if($tile instanceof Sign){
                				$text = $tile->getText();
                    				if($text[0] === F::GREEN . "[STATUS]"){
                        				$tps = $this->plugin->getServer()->getTicksPerSecond();
                        				$p = count($this->plugin->getServer()->getOnlinePlayers());
                        				$full = $this->plugin->getServer()->getMaxPlayers();
                        				$count = $this->countable++; //For debug
                        				$load = $this->plugin->getServer()->getTickUsage();
                        				$tile->setText(F::GREEN . "[STATUS]", F::YELLOW . "TPS: [".  $tps . "]", F::AQUA . "ONLINE: " . F::GREEN . $p . F::WHITE . "/" . F::RED . $full . "", F::GOLD . "LOAD: " . F::DARK_BLUE . $load . " %");
                    				}
                			}
            			}
			}
		}
    	}
}
