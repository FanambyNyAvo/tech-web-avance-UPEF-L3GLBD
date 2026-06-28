<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Chroniques du Plateau</title>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;800&family=Crimson+Text:ital,wght@0,400;0,600;1,400&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/style.css">
  <link rel="stylesheet" href="assets/mode.css">
</head>
<body>
<div class="scroll">

  <h1 class="title">⚔ Chroniques du Plateau ⚔</h1>
  <p class="subtitle">Un récit de bataille en terres oubliées</p>
  <hr class="divider">

  <?php if (!$submitted): ?>
    <?php require 'templates/config.php'; ?>
  <?php else: ?>
    <?php if ($mode === 'simulation'): ?>
      <?php require 'templates/game_simulation.php'; ?>
    <?php else: ?>
      <?php require 'templates/game_joueur.php'; ?>
    <?php endif; ?>
  <?php endif; ?>

</div>
</body>
</html>