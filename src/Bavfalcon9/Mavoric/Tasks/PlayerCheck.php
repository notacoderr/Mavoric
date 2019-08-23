<?php

namespace Bavfalcon9\Mavoric\Tasks;
use pocketmine\scheduler\Task;
use Bavfalcon9\Mavoric\Mavoric;
use pocketmine\Player;
use pocketmine\Level\Position;
use pocketmine\math\Vector3;

class PlayerCheck extends Task {
    private $mav;
    private $timer;
    private $target;
    private $npc; // Fake player entity, npc is easier to refer too.
    private $stopAt;
    private $hasTeleported = false;

    public function __construct(Mavoric $mavoric, int $time, Player $target, String $fakePlayer) {
        $this->mav = $mavoric;
        $this->stopAt = $time;
        $this->target = $target;
        $this->npc = $fakePlayer;
    }

    public function onRun(int $tick) {
        try {
            $this->timer++;

            // Check timer
            if ($this->timer >= $this->stopAt) {
                $this->mav->killTask($this->npc);
                $fakePlayer = $this->mav->getPlugin()->getServer()->getPlayer($this->npc);
                if ($fakePlayer === null) return;
                else {
                    $fakePlayer->kill();
                    $fakePlayer->close('', 'Mavoric test closed.');
                }
            }

            $fakePlayer = $this->mav->getPlugin()->getServer()->getPlayer($this->npc);
            if ($fakePlayer === null) return;
            if ($this->target === null) return $this->timer = $this->stopAt;

            $oldPos = $this->target->getPosition();
            $newPos = new Position($oldPos->getX(), $oldPos->getY() + 3, $oldPos->getZ(), $this->target->getLevel());

            if ($fakePlayer->getLevel()->getName() !== $this->target->getLevel()->getName()) $fakePlayer->teleport($newPos);

            $fakePlayer->setNameTag('§4[AC] Mavoric');
            $fakePlayer->setHealth(20);
            $fakePlayer->setScale(1);
            if (!$this->hasTeleported) {
                $fakePlayer->teleport($newPos);
                $this->hasTeleported = true;
            }
            $this->mav->updateMotion($fakePlayer, new Vector3($oldPos->getX(), $oldPos->getY() + 4, $oldPos->getZ() + 1), $this->target);

        } catch(\ParseError $e) {
            return;
        } catch(\UnexpectedValueException $e) {
            return;
        } catch(\ErrorException $e) {
            return;
        } catch(\Error $e) {
            return;
        } catch(\BadMethodCallException $e) {
            return;
        } catch(\BadFunctionCallException $e) {
            return;
        } catch(\Exception $e) {
            return;
        }

        // Reference https://github.com/falkirks/Specter/blob/master/src/specter/Specter.php pls thx

    }
}