<?php
namespace SignStatus;

use pocketmine\level\Level;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\level\Position;
use pocketmine\block\Block;
use pocketmine\Server;

/*
##########################################
###############by xpyctum#################
##########################################
*/
class SignStatus extends PluginBase implements Listener{

	public function onEnable(){
		@mkdir($this->getDataFolder());
		$this->sign = new Config($this->getDataFolder()."sign.yml", Config::YAML, [
			"sign" => [
				"enabled" => false,
				"x" => 0,
				"y" => 0,
				"z" => 0,
				"level" => "world"
			]
		]);
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$time = 100;
		$this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), $time);
	}
	public function onDisable()
	{
		
	}
	public function enabled(){
		return $this->sign->get("sign")['enabled'];
	}
	public function level(){
		return $this->sign->get("sign")['level'];
	}
	public function getThisSignX(){
		return $this->sign->get("sign")['x'];
	}
	public function getThisSignY(){
		return $this->sign->get("sign")['y'];
	}
	public function getThisSignZ(){
		return $this->sign->get("sign")['z'];
	}
	public function onSignChange(SignChangeEvent $event){
	$sign = new Config($this->getDataFolder()."sign.yml", Config::YAML);
	$player = $event->getPlayer();
		if(strtolower(trim($event->getLine(0))) == "status" || strtolower(trim($event->getLine(0))) == "[status]"){
			if($player->hasPermission("signstatus")){
				$tps = $this->getServer()->getTicksPerSecond();
				$p = count($this->getServer()->getOnlinePlayers());
				$level = $event->getBlock()->getLevel()->getName();
				$full = $this->getServer()->getMaxPlayers();
				$event->setLine(0,"[STATUS]");
				$event->setLine(1,"TPS: [".$tps."]");
				$event->setLine(2,"ONLINE: ".$p."/".$full."");
				$event->setLine(3,"******");
				$arr = array(
				"sign" => array(
					"x" => $event->getBlock()->getX(),
					"y" => $event->getBlock()->getY(),
					"z" => $event->getBlock()->getZ(),
					"enabled" => true,
					"level" => $level
					)
				);
				$sign->setAll($arr);
				$sign->save();

				$sign->reload();
				$sign->reload(); //It's very important! Because if we don't use this, plugin will be use old config or DON't update IF we create first sign!
				$event->getPlayer()->sendMessage("[SignStatus] You successfully created status sign!");
			}else{
				$player->sendMessage("[SignStatus] You don't have permissions!");
				$event->setLine(0,"[SORRY]");
				$event->setLine(1,"YOU");
				$event->setLine(2,"DON'T HAVE");
				$event->setLine(3,"PERMISSIONS");
			}
			
		
		}
	}
	public function onPlayerBreakBlock(BlockBreakEvent $event)
    {
        if ($event->getBlock()->getID() === Item::SIGN || $event->getBlock()->getID() === Item::WALL_SIGN || $event->getBlock()->getID() === Item::SIGN_POST) {
            $signt = $event->getBlock();
            if (($tile = $signt->getLevel()->getTile($signt))){
				if($tile instanceof Sign){
				 if($event->getBlock()->getX() == $this->getThisSignX() || $event->getBlock()->getY() == $this->getThisSignY() || $event->getBlock()->getZ() == $this->getThisSignZ()){
					$arr = array(
					"sign" => array(
						"x" => $event->getBlock()->getX(),
						"y" => $event->getBlock()->getY(),
						"z" => $event->getBlock()->getZ(),
						"enabled" => false,
						"level" => "world"
					)
				);
				$sign = new Config($this->getDataFolder()."sign.yml", Config::YAML);
				$sign->setAll($arr);
				$sign->save();
				$sign->reload();
				 }
				}
			}
		}
	}
}
