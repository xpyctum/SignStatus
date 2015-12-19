<?php
namespace SignStatus;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\tile\Tile;
use pocketmine\utils\Config;
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
//TODO: Make configurable format of sign
class SignStatus extends PluginBase implements Listener{

    /** @var Config sign */
    public $sign;

    /** @var Config translation */
    public $translation;

    /** @var Config config */
    public $config;

    /** @var Config config */
    public $format;

    /** @var string  */
    public $prefix = "§4[§2SignStatus§4]§6 ";

    public function onEnable(){
        if(!is_dir($this->getDataFolder())){
            @mkdir($this->getDataFolder());
            //Use default, not PM.
        }

        $this->saveResource("sign.yml");
        $this->saveResource("translations.yml");
        $this->saveResource("config.yml");
        $this->saveResource("format.yml");

        $this->sign = new Config($this->getDataFolder()."sign.yml", Config::YAML); //FIXED !
        $this->translation = new Config($this->getDataFolder()."translations.yml",Config::YAML);
        $this->config = new Config($this->getDataFolder()."config.yml",Config::YAML);
        $this->format = new Config($this->getDataFolder()."format.yml",Config::YAML);
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
     * @param SignChangeEvent $event
     */
    public function onSignChange(SignChangeEvent $event){
        $player = $event->getPlayer();
        if(strtolower(trim($event->getLine(0))) == "status" || strtolower(trim($event->getLine(0))) == "[status]"){
            if($player->hasPermission("signstatus") or $player->hasPermission("signstatus.create")){
                $tps = $this->getServer()->getTicksPerSecond();
                $p = count($this->getServer()->getOnlinePlayers());
                $level = $event->getBlock()->getLevel()->getName();
                $full = $this->getServer()->getMaxPlayers();
                $load = $this->getServer()->getTickUsage();
                $format = $this->format->getAll();

                for ($x = 0; $x <= 3; $x++) {
                    $v = $format["format"][$x+1];
                    $v = str_replace("{ONLINE}", $p, $v);
                    $v = str_replace("{MAX_ONLINE}", $full, $v);
                    $v = str_replace("{WORLD_NAME}", $level, $v);
                    $v = str_replace("{TPS}", $tps, $v);
                    $v = str_replace("{SERVER_LOAD}", $load, $v);
                    $event->setLine($x,$v);
                }
                //$event->setText(F::GREEN."[STATUS]",F::YELLOW."TPS: [$tps]",F::AQUA."ONLINE: ".F::GREEN.$p.F::WHITE."/".F::RED.$full.",".F::GOLD."******");
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
                    if ($event->getBlock()->getX() == $this->sign->getNested("sign.x") && $event->getBlock()->getY() == $this->sign->getNested("sign.y") && $event->getBlock()->getZ() == $this->sign->getNested("sign.z")) {
                        if($tile->getText()[0] == strtolower($this->format->getAll()["format"][1])) {
                            if ($event->getPlayer()->hasPermission("signstatus.break")) {
                                $event->getPlayer()->sendMessage($this->prefix . $this->translation->get("sign_destroyed"));
                            } else {
                                $event->getPlayer()->sendMessage($this->prefix . $this->translation->get("sign_no_perms"));
                                $event->setCancelled();
                            }
                        }
                    }
                }
            }
        }
    }


}
