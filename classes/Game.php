<?php

require_once 'Character.php';
require_once 'Hero.php';
require_once 'Enemy.php';
require_once 'Potion.php';
require_once 'Board.php';

class Game {

    private Hero   $hero;
    private Enemy  $enemy;
    private Board  $board;
    private Potion $potion;
    private int    $turn = 0;
    private array  $snapshots = [];

    public function __construct(int $heroHp = 30, int $heroAtk = 7, int $enemyHp = 20, int $enemyAtk = 5) {
        $this->board = new Board(10);


        $hx = rand(0, 9);
        $hy = rand(0, 9);
        $this->hero = new Hero($hx, $hy, $heroHp, $heroAtk);

        do {
            $ex = rand(0, 9);
            $ey = rand(0, 9);
        } while ($ex === $hx && $ey === $hy);
        $this->enemy = new Enemy($ex, $ey, $enemyHp, $enemyAtk);

        do {
            $px = rand(0, 9);
            $py = rand(0, 9);
        } while (
            ($px === $hx && $py === $hy) ||
            ($px === $ex && $py === $ey)
        );

        $this->potion = new Potion($px, $py, healAmount: 10);
    }

    public function simulate(): array {

        $this->snapshots[] = $this->buildSnapshot([], 'intro');
        while ($this->hero->isAlive() && $this->enemy->isAlive()) {
            $this->turn++;
            $events = [];

            $hpRatio       = $this->hero->getHp() / $this->hero->getMaxHp();
            $potionDispo   = !$this->potion->isCollected();
            $fuiteVersPotion = $hpRatio <= 0.5 && $potionDispo;

            if ($fuiteVersPotion) {

                $distPotion = $this->board->distance(
                    $this->hero->getX(), $this->hero->getY(),
                    $this->potion->getX(), $this->potion->getY()
                );

                if ($distPotion === 0) {

                    $heal   = $this->potion->collect();
                    $healed = $this->hero->heal($heal);
                    $events[] = ['type' => 'potion', 'who' => 'hero',
                                 'msg'  => "{$this->hero->getName()} ramasse la potion ! +{$healed} HP"];
                } else {

                    $dir = $this->getDirectionToward(
                        $this->hero->getX(), $this->hero->getY(),
                        $this->potion->getX(), $this->potion->getY()
                    );
                    $this->hero->move($dir, $this->board->getSize());
                    $events[] = ['type' => 'flee', 'who' => 'hero',
                                 'msg'  => "⚠️ {$this->hero->getName()} fuit vers la potion ! ({$dir})"];
     
                    if ($this->potion->isAtPosition($this->hero->getX(), $this->hero->getY())) {
                        $heal   = $this->potion->collect();
                        $healed = $this->hero->heal($heal);
                        $events[] = ['type' => 'potion', 'who' => 'hero',
                                     'msg'  => "{$this->hero->getName()} ramasse la potion ! +{$healed} HP"];
                    }
                }
            } else {

                $dist = $this->board->distance(
                    $this->hero->getX(), $this->hero->getY(),
                    $this->enemy->getX(), $this->enemy->getY()
                );

                if ($dist > 1) {
                    $dir = $this->getDirectionToward(
                        $this->hero->getX(), $this->hero->getY(),
                        $this->enemy->getX(), $this->enemy->getY()
                    );
                    $this->hero->move($dir, $this->board->getSize());
                    $events[] = ['type' => 'move', 'who' => 'hero',
                                 'msg'  => "{$this->hero->getName()} avance vers l'ennemi ({$dir})"];

                    if ($this->potion->isAtPosition($this->hero->getX(), $this->hero->getY())) {
                        $heal   = $this->potion->collect();
                        $healed = $this->hero->heal($heal);
                        $events[] = ['type' => 'potion', 'who' => 'hero',
                                     'msg'  => "{$this->hero->getName()} ramasse la potion au passage ! +{$healed} HP"];
                    }
                } else {
                    $dmg = $this->hero->attack($this->enemy);
                    $events[] = ['type' => 'attack', 'who' => 'hero',
                                 'msg'  => "{$this->hero->getName()} frappe {$this->enemy->getName()} pour {$dmg} dégâts !"];

                    if ($this->enemy->checkRage()) {
                        $events[] = ['type' => 'rage', 'who' => 'enemy',
                                     'msg'  => "{$this->enemy->getName()} entre en RAGE ! ATK → {$this->enemy->getAttack()}"];
                    }
                }
            }

            // --- Tour ennemi ---
            if ($this->enemy->isAlive()) {
                $dist2 = $this->board->distance(
                    $this->enemy->getX(), $this->enemy->getY(),
                    $this->hero->getX(), $this->hero->getY()
                );

                if ($dist2 > 1) {
                    $dir2 = $this->getDirectionToward(
                        $this->enemy->getX(), $this->enemy->getY(),
                        $this->hero->getX(), $this->hero->getY()
                    );
                    $this->enemy->move($dir2, $this->board->getSize());
                    $events[] = ['type' => 'move', 'who' => 'enemy',
                                 'msg'  => "{$this->enemy->getName()} se rapproche ({$dir2})"];
                } else {
                    $dmg2 = $this->enemy->attack($this->hero);
                    $events[] = ['type' => 'attack', 'who' => 'enemy',
                                 'msg'  => "{$this->enemy->getName()} riposte pour {$dmg2} dégâts !"];
                }
            }

            $this->snapshots[] = $this->buildSnapshot($events, 'ongoing');
        }

        $winner = $this->hero->isAlive() ? 'hero' : 'enemy';
        $this->snapshots[] = $this->buildSnapshot(
            [['type' => 'end', 'who' => $winner,
              'msg'  => $winner === 'hero'
                ? "🏆 {$this->hero->getName()} remporte la bataille en {$this->turn} tours !"
                : "💀 {$this->hero->getName()} est vaincu par {$this->enemy->getName()}..."]],
            'end'
        );

        return $this->snapshots;
    }

    private function buildSnapshot(array $events, string $phase): array {
        return [
            'turn'   => $this->turn,
            'phase'  => $phase,
            'hero'   => [
                'name'   => $this->hero->getName(),
                'hp'     => $this->hero->getHp(),
                'maxHp'  => $this->hero->getMaxHp(),
                'attack' => $this->hero->getAttack(),
                'x'      => $this->hero->getX(),
                'y'      => $this->hero->getY(),
                'alive'  => $this->hero->isAlive(),
            ],
            'enemy'  => [
                'name'    => $this->enemy->getName(),
                'hp'      => $this->enemy->getHp(),
                'maxHp'   => $this->enemy->getMaxHp(),
                'attack'  => $this->enemy->getAttack(),
                'x'       => $this->enemy->getX(),
                'y'       => $this->enemy->getY(),
                'alive'   => $this->enemy->isAlive(),
                'enraged' => $this->enemy->isEnraged(),
            ],
            'potion' => [
                'x'         => $this->potion->getX(),
                'y'         => $this->potion->getY(),
                'collected' => $this->potion->isCollected(),
            ],
            'events' => $events,
        ];
    }

    private function getDirectionToward(int $fromX, int $fromY, int $toX, int $toY): string {
        $dx = $toX - $fromX;
        $dy = $toY - $fromY;
        if (abs($dx) >= abs($dy)) return $dx > 0 ? 'right' : 'left';
        return $dy > 0 ? 'down' : 'up';
    }
}
