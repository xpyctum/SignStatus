<?php
namespace SignStatus;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\utils\Config;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\utils\TextFormat as F;

/*
         -        ████──██─██      -
          -       █──██──███      -
            -     ████────█      -
              -   █──██───█    -
                - ████────█  -
                  ------------------
██─██─████─██─██─████─███─█─█─█───█
─███──█──█──███──█──█──█──█─█─██─██
──█───████───█───█─────█──█─█─█─█─█
─███──█──────█───█──█──█──█─█─█───█
██─██─█──────█───████──█──███─█───█
*/
//TODO: Make multisigns and configurable format of sign
class SignStatus extends PluginBase implements Listener{

    /** @var Config sign */
    public $sign;

    /** @var Config translation */
    public $translation;

    /** @var Config config */
    public $config;

    /** @var string  */
    public $prefix = "§4[§2SignStatus§4]§6 ";

    public function onEnable(){
        if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder());
            //Use default, not PM.
        }

        $this->saveResource("sign.yml");
        $this->saveResource("translation.yml");
        $this->saveResource("config.yml");

        $this->sign = new Config($this->getDataFolder()."sign.yml", Config::YAML); //FIXED !
        $this->translation = new Config($this->getDataFolder()."translation.yml",Config::YAML);
        $this->config = new Config($this->getDataFolder()."config.yml",Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $time = $this->config->get("time");
        if(!(is_numeric($time))){
            $time = 20;
            $this->getLogger()->alert("Can't read time for update sign! Please, check your config file! Default: ".F::AQUA." 1 ".F::WHITE." second");
        }else{ $time = $time * 20; }
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new Task($this), $time);
        $this->getLogger()->notice(F::GREEN."SignStatus loaded");

    }

    public function onDisable(){
        $this->getLogger()->notice(F::RED."SignStatus disabled");
    }

    /**
     * @deprecated
     * @return mixed
     */
    public function enabled(){
        return $this->sign->get("sign")['enabled'];
    }
    /**
     * @deprecated
     * @return mixed
     */
    public function level(){
        return $this->sign->get("sign")['level'];
    }
    /**
     * @deprecated
     * @return mixed
     */
    public function getThisSignX(){
        return $this->sign->get("sign")['x'];
    }
    /**
     * @deprecated
     * @return mixed
     */
    public function getThisSignY(){
        return $this->sign->get("sign")['y'];
    }
    /**
     * @deprecated
     * @return mixed
     */
    public function getThisSignZ(){
        return $this->sign->get("sign")['z'];
    }


    /**
     * @param SignChangeEvent $event
     */
    public function onSignChange(SignChangeEvent $event){
        $player = $event->getPlayer();
        if(strtolower(trim($event->getLine(0))) == "status" || strtolower(trim($event->getLine(0))) == "[status]"){
            if($player->hasPermission("signstatus")){
                $tps = $this->getServer()->getTicksPerSecond();
                $p = count($this->getServer()->getOnlinePlayers());
                $level = $event->getBlock()->getLevel()->getName();
                $full = $this->getServer()->getMaxPlayers();
                $event->setLine(0,F::GREEN."[STATUS]");
                $event->setLine(1,F::YELLOW."TPS: [".$tps."]");
                $event->setLine(2,F::AQUA."ONLINE: ".F::GREEN.$p.F::WHITE."/".F::RED.$full."");
                $event->setLine(3,F::GOLD."******");

                $this->sign->setNested("sign.x", $event->getBlock()->getX());
                $this->sign->setNested("sign.y", $event->getBlock()->getY());
                $this->sign->setNested("sign.z", $event->getBlock()->getZ());
                $this->sign->setNested("sign.enabled", true);
                $this->sign->setNested("sign.level", $level);
                $this->sign->save();
                $this->sign->reload();
                $event->getPlayer()->sendMessage($this->prefix.$this->translation->get("sign_created"));
            }else{
                $player->sendMessage($this->prefix.$this->translation->get("sign_no_perms"));
                $event->setCancelled();
            }
        }
    }

    /**
     * @param BlockBreakEvent $event
     */
    public function onPlayerBreakBlock(BlockBreakEvent $event){
        if ($event->getBlock()->getID() == Item::SIGN || $event->getBlock()->getID() == Item::WALL_SIGN || $event->getBlock()->getID() == Item::SIGN_POST) {
            $signt = $event->getBlock();
            if (($tile = $signt->getLevel()->getTile($signt))){
                if($tile instanceof Sign) {
                    if ($event->getBlock()->getX() == $this->sign->getNested("sign.x") || $event->getBlock()->getY() == $this->sign->getNested("sign.y") || $event->getBlock()->getZ() == $this->sign->getNested("sign.z")) {
                        if($event->getPlayer()->hasPermission("signstatus.break")) {
                            $this->sign->setNested("sign.x", $event->getBlock()->getX());
                            $this->sign->setNested("sign.y", $event->getBlock()->getY());
                            $this->sign->setNested("sign.z", $event->getBlock()->getZ());
                            $this->sign->setNested("sign.enabled", false);
                            $this->sign->setNested("sign.level", "world");
                            $this->sign->save();
                            $this->sign->reload();
                            $event->getPlayer()->sendMessage($this->prefix.$this->translation->get("sign_destroyed"));
                        }else{
                            $event->getPlayer()->sendMessage($this->prefix.$this->translation->get("sign_no_perms"));
                            $event->setCancelled();
                        }
                    }
                }
            }
        }
    }
}
