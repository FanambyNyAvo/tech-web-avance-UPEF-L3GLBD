<div class="stats-row">
  <div class="stat-card" id="hero-card">
    <div class="stat-name">⚔ <span id="hero-name">—</span></div>
    <div class="stat-badge">Héros</div>
    <div class="hp-bar-wrap"><div class="hp-bar" id="hero-bar"></div></div>
    <div class="hp-label" id="hero-hp-label"></div>
    <div class="atk-label" id="hero-atk-label"></div>
  </div>
  <div class="stat-card enemy-card" id="enemy-card">
    <div class="stat-name">💀 <span id="enemy-name">—</span></div>
    <div class="stat-badge">Ennemi</div>
    <div class="hp-bar-wrap"><div class="hp-bar" id="enemy-bar"></div></div>
    <div class="hp-label" id="enemy-hp-label"></div>
    <div class="atk-label" id="enemy-atk-label"></div>
  </div>
</div>

<div class="arena">
  <div class="board-wrap">
    <div class="board-label">— CHAMP DE BATAILLE —</div>
    <div class="board" id="board"></div>
  </div>
  <div class="log-wrap">
    <div class="log-title">— CHRONIQUE DU TOUR —</div>
    <div class="log-box" id="log-box"></div>
  </div>
</div>

<div class="btn-row">
  <button class="btn" id="btn-next" onclick="nextTurn()">Tour suivant →</button>
  <button class="btn restart" id="btn-restart" onclick="location.href=location.pathname" style="display:none">↺ Reconfigurer</button>
</div>
<div class="turn-counter" id="turn-counter">Tour 0 / <?= count($snapshots) - 1 ?></div>

<script>
const SNAPSHOTS  = <?= $json ?>;
const BOARD_SIZE = 10;
let current = 0;

function renderBoard(snap) {
  const board = document.getElementById('board');
  board.innerHTML = '';
  for (let row = 0; row < BOARD_SIZE; row++) {
    for (let col = 0; col < BOARD_SIZE; col++) {
      const cell = document.createElement('div');
      cell.className = 'cell';

      const isHero   = col === snap.hero.x  && row === snap.hero.y;
      const isEnemy  = col === snap.enemy.x && row === snap.enemy.y;
      const isPotion = !snap.potion.collected && col === snap.potion.x && row === snap.potion.y;

      if (isHero)        { cell.innerHTML = '<img src="assets/hero.png" style="width:32px;height:32px;object-fit:contain;">'; cell.classList.add('hero-cell'); }
      else if (isEnemy) {
  cell.innerHTML = snap.enemy.alive
    ? '<img src="assets/enemy.png" style="width:32px;height:32px;object-fit:contain;">'
    : '<img src="assets/enemy_dead.png" style="width:32px;height:32px;object-fit:contain;">';
  cell.classList.add('enemy-cell');
}
      else if (isPotion) { cell.innerHTML = '<img src="assets/potion.png" style="width:32px;height:32px;object-fit:contain;">'; cell.classList.add('potion-cell'); }

      board.appendChild(cell);
    }
  }
}

function renderStats(snap) {
  document.getElementById('hero-name').textContent = snap.hero.name;

  const heroHpPct = Math.max(0, Math.round(snap.hero.hp / snap.hero.maxHp * 100));
  const heroBar   = document.getElementById('hero-bar');
  heroBar.style.width = heroHpPct + '%';
  heroBar.className = 'hp-bar' + (heroHpPct <= 25 ? ' low' : heroHpPct <= 50 ? ' mid' : '');
  document.getElementById('hero-hp-label').textContent  = `HP : ${snap.hero.hp} / ${snap.hero.maxHp}`;
  document.getElementById('hero-atk-label').textContent = `⚔ Attaque : ${snap.hero.attack}`;

  document.getElementById('enemy-name').textContent = snap.enemy.name;

  const enemyHpPct = Math.max(0, Math.round(snap.enemy.hp / snap.enemy.maxHp * 100));
  const enemyBar   = document.getElementById('enemy-bar');
  enemyBar.style.width = enemyHpPct + '%';
  enemyBar.className = 'hp-bar' + (enemyHpPct <= 25 ? ' low' : enemyHpPct <= 50 ? ' mid' : '');
  document.getElementById('enemy-hp-label').textContent = `HP : ${snap.enemy.hp} / ${snap.enemy.maxHp}`;

  const rageTag = snap.enemy.enraged ? '<span class="rage-tag">RAGE</span>' : '';
  document.getElementById('enemy-atk-label').innerHTML = `⚔ Attaque : ${snap.enemy.attack}${rageTag}`;
}

function renderLog(snap) {
  const box = document.getElementById('log-box');
  box.innerHTML = '';

  if (snap.phase === 'intro') {
    box.innerHTML = `
      <p class="intro-text">
        Le héros <strong>${snap.hero.name}</strong> entre sur le champ de bataille.<br>
        Un <strong>${snap.enemy.name}</strong> l'attend en embuscade.<br>
        Une potion de soin brille sur le sol, en attente du premier à la trouver.<br><br>
        <em>Que la chronique commence…</em>
      </p>`;
    return;
  }

  const header = document.createElement('div');
  header.className = 'turn-header';
  header.textContent = snap.phase === 'end' ? '— Fin de la Bataille —' : `— Tour ${snap.turn} —`;
  box.appendChild(header);

  snap.events.forEach(ev => {
    const line = document.createElement('div');
    line.className = `event-line type-${ev.type}`;
    line.textContent = ev.msg;
    box.appendChild(line);
  });
}

function nextTurn() {
  if (current >= SNAPSHOTS.length - 1) return;
  current++;
  const snap = SNAPSHOTS[current];

  renderBoard(snap);
  renderStats(snap);
  renderLog(snap);

  document.getElementById('turn-counter').textContent = `Tour ${current} / ${SNAPSHOTS.length - 1}`;

  if (snap.phase === 'end') {
    document.getElementById('btn-next').disabled = true;
    document.getElementById('btn-restart').style.display = 'inline-block';
  }
}

renderBoard(SNAPSHOTS[0]);
renderStats(SNAPSHOTS[0]);
renderLog(SNAPSHOTS[0]);
</script>
