<?php
require_once 'classes/Game.php';

$submitted = isset($_POST['lancer']);
$mode      = 'simulation';

if ($submitted) {
    $heroHp   = max(1, min(100, (int)($_POST['hero_hp']   ?? 30)));
    $heroAtk  = max(1, min(50,  (int)($_POST['hero_atk']  ?? 7)));
    $enemyHp  = max(1, min(100, (int)($_POST['enemy_hp']  ?? 20)));
    $enemyAtk = max(1, min(50,  (int)($_POST['enemy_atk'] ?? 5)));
    $mode     = isset($_POST['mode']) && $_POST['mode'] === 'joueur' ? 'joueur' : 'simulation';

    if ($mode === 'simulation') {
        $game      = new Game($heroHp, $heroAtk, $enemyHp, $enemyAtk);
        $snapshots = $game->simulate();
        $json      = json_encode($snapshots, JSON_UNESCAPED_UNICODE);
    }
}

require 'templates/layout.php';