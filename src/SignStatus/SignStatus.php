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
		$this->sign->save();
	}
	// public function onBlockPlace(BlockPlaceEvent $event){
		//$name = $event->getPlayer()->getDisplayName();
		// $player = $event->getPlayer();
		// if($event->getBlock()->getID() === Item::SIGN || $event->getBlock()->getID() === Item::WALL_SIGN || $event->getBlock()->getID() === Item::SIGN_POST){
			// $sign = $event->getLevel()->getTile(new Vector($event->getBlock()->getX(), $event->getBlock()->getY(), $event->getBlock()->getZ()));
		// }
	// }
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
	$this->sign = new Config($this->getDataFolder()."sign.yml", Config::YAML);
		if(strtolower(trim($event->getLine(0))) == "status" || strtolower(trim($event->getLine(0))) == "[status]"){
			$tps = $this->getServer()->getTicksPerSecond();
			$p = count($this->getServer()->getOnlinePlayers());
			$level = $event->getBlock()->getLevel();
			$full = $this->getServer()->getMaxPlayers();
			$event->setLine(0,"[STATUS]");
			$event->setLine(1,"TPS: [".$tps."]");
			$event->setLine(2,"ONLINE: ".$p."/".$full."");
			$event->setLine(3,"******");
			$this->sign->set("x",$event->getBlock()->getX());			
			$this->sign->set("y",$event->getBlock()->getY());			
			$this->sign->set("z",$event->getBlock()->getZ());			
			$this->sign->set("enabled","true");			
			$this->sign->set("level",$level);			
			$this->sign->save();
		}
	}
}
?>