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
		if($val == "true" || $val == true){
			$x = $this->plugin->sign->get("sign")["x"];
			$y = $this->plugin->sign->get("sign")["y"];
			$z = $this->plugin->sign->get("sign")["z"];
			$lvz = $this->plugin->sign->get("sign")["level"];
			$tps = Server::getInstance()->getTicksPerSecond();
			$p = count(Server::getInstance()->getOnlinePlayers());
			$full = Server::getInstance()->getMaxPlayers();
			$level = Server::getInstance()->getLevelByName($lvz);
            if($level instanceof Level) {
                $sign = $level->getTile(new Vector3($x, $y, $z));
                $count = $this->countable++; //For debug
                if ($sign instanceof Sign) {
                    $sign->setText(F::GREEN."[STATUS]", F::YELLOW."TPS: [".$tps."]", F::AQUA."ONLINE: ".F::GREEN.$p.F::WHITE."/".F::RED.$full."", F::GOLD.$count);
                }
            }
		}
    }
}