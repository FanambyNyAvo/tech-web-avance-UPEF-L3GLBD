<!-- STATS -->
<div class="stats-row">
  <div class="stat-card" id="hero-card">
    <div class="stat-name">⚔ <span id="hero-name">Aldric</span></div>
    <div class="stat-badge">Héros</div>
    <div class="hp-bar-wrap"><div class="hp-bar" id="hero-bar"></div></div>
    <div class="hp-label" id="hero-hp-label"></div>
    <div class="atk-label" id="hero-atk-label"></div>
  </div>
  <div class="stat-card enemy-card" id="enemy-card">
    <div class="stat-name">💀 <span id="enemy-name">Gobelin</span></div>
    <div class="stat-badge">Ennemi</div>
    <div class="hp-bar-wrap"><div class="hp-bar" id="enemy-bar"></div></div>
    <div class="hp-label" id="enemy-hp-label"></div>
    <div class="atk-label" id="enemy-atk-label"></div>
  </div>
</div>

<!-- CONTRÔLES CLAVIER -->
<div class="controls-hint">
  <span class="key">Z</span> Haut &nbsp;
  <span class="key">S</span> Bas &nbsp;
  <span class="key">Q</span> Gauche &nbsp;
  <span class="key">D</span> Droite &nbsp;
  <span class="key-atk">⚔</span> Attaque auto si adjacent
</div>

<!-- ARÈNE -->
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

<!-- CONTRÔLES -->
<div class="btn-row">
  <button class="btn restart" id="btn-restart" onclick="location.href=location.pathname">↺ Reconfigurer</button>
</div>
<div class="turn-counter" id="turn-counter">Tour 0</div>

<script>
// ── État du jeu ──────────────────────────────────────────
const BOARD_SIZE = 10;
let turn = 0;
let gameOver = false;

const state = {
  hero: {
    name:    'Aldric',
    hp:      <?= $heroHp ?>,
    maxHp:   <?= $heroHp ?>,
    attack:  <?= $heroAtk ?>,
    x: Math.floor(Math.random() * BOARD_SIZE),
    y: Math.floor(Math.random() * BOARD_SIZE),
    alive: true
  },
  enemy: {
    name:    'Gobelin',
    hp:      <?= $enemyHp ?>,
    maxHp:   <?= $enemyHp ?>,
    attack:  <?= $enemyAtk ?>,
    x: 0, y: 0,
    alive: true,
    enraged: false
  },
  potion: {
    x: 0, y: 0,
    collected: false,
    amount: 10
  }
};

// Positions aléatoires pour ennemi et potion (pas sur le héros)
function randomFreePos() {
  let x, y;
  do {
    x = Math.floor(Math.random() * BOARD_SIZE);
    y = Math.floor(Math.random() * BOARD_SIZE);
  } while (
    (x === state.hero.x && y === state.hero.y) ||
    (x === state.enemy.x && y === state.enemy.y)
  );
  return { x, y };
}

const ePos = randomFreePos();
state.enemy.x = ePos.x;
state.enemy.y = ePos.y;
const pPos = randomFreePos();
state.potion.x = pPos.x;
state.potion.y = pPos.y;

// ── Rendu ────────────────────────────────────────────────
function renderBoard() {
  const board = document.getElementById('board');
  board.innerHTML = '';
  for (let row = 0; row < BOARD_SIZE; row++) {
    for (let col = 0; col < BOARD_SIZE; col++) {
      const cell = document.createElement('div');
      cell.className = 'cell';

      const isHero   = col === state.hero.x  && row === state.hero.y;
      const isEnemy  = col === state.enemy.x && row === state.enemy.y && state.enemy.alive;
      const isPotion = !state.potion.collected && col === state.potion.x && row === state.potion.y;

    if (isHero)        { cell.innerHTML = '<img src="assets/hero.png" style="width:32px;height:32px;object-fit:contain;">'; cell.classList.add('hero-cell'); }
    else if (isEnemy)  { cell.innerHTML = '<img src="assets/enemy.png" style="width:32px;height:32px;object-fit:contain;">'; cell.classList.add('enemy-cell'); }
    else if (isPotion) { cell.innerHTML = '<img src="assets/potion.png" style="width:32px;height:32px;object-fit:contain;">'; cell.classList.add('potion-cell'); }

      board.appendChild(cell);
    }
  }
}

function renderStats() {
  // Héros
  const heroPct = Math.max(0, Math.round(state.hero.hp / state.hero.maxHp * 100));
  const heroBar = document.getElementById('hero-bar');
  heroBar.style.width = heroPct + '%';
  heroBar.className   = 'hp-bar' + (heroPct <= 25 ? ' low' : heroPct <= 50 ? ' mid' : '');
  document.getElementById('hero-hp-label').textContent  = `HP : ${state.hero.hp} / ${state.hero.maxHp}`;
  document.getElementById('hero-atk-label').textContent = `⚔ Attaque : ${state.hero.attack}`;

  // Ennemi
  const enemyPct = Math.max(0, Math.round(state.enemy.hp / state.enemy.maxHp * 100));
  const enemyBar = document.getElementById('enemy-bar');
  enemyBar.style.width = enemyPct + '%';
  enemyBar.className   = 'hp-bar' + (enemyPct <= 25 ? ' low' : enemyPct <= 50 ? ' mid' : '');
  document.getElementById('enemy-hp-label').textContent = `HP : ${state.enemy.hp} / ${state.enemy.maxHp}`;
  const rageTag = state.enemy.enraged ? '<span class="rage-tag">RAGE</span>' : '';
  document.getElementById('enemy-atk-label').innerHTML  = `⚔ Attaque : ${state.enemy.attack}${rageTag}`;
}

function addLog(msg, type = 'move') {
  const box  = document.getElementById('log-box');
  const line = document.createElement('div');
  line.className   = `event-line type-${type}`;
  line.textContent = msg;
  box.prepend(line); // plus récent en haut
}

// ── Distance Manhattan ────────────────────────────────────
function distance(x1, y1, x2, y2) {
  return Math.abs(x1 - x2) + Math.abs(y1 - y2);
}

// ── Tour de l'ennemi ─────────────────────────────────────
function enemyTurn() {
  if (!state.enemy.alive) return;

  const dist = distance(state.enemy.x, state.enemy.y, state.hero.x, state.hero.y);

  if (dist <= 1) {
    // Attaque
    state.hero.hp = Math.max(0, state.hero.hp - state.enemy.attack);
    addLog(`💀 ${state.enemy.name} riposte → ${state.enemy.attack} dégâts !`, 'attack');

    if (state.hero.hp <= 0) {
      state.hero.alive = false;
      endGame(false);
    }
  } else {
    // Se rapproche
    const dx = state.hero.x - state.enemy.x;
    const dy = state.hero.y - state.enemy.y;
    if (Math.abs(dx) >= Math.abs(dy)) {
      state.enemy.x += dx > 0 ? 1 : -1;
    } else {
      state.enemy.y += dy > 0 ? 1 : -1;
    }
    addLog(`${state.enemy.name} se rapproche…`, 'move');
  }

  // Rage
  if (!state.enemy.enraged && state.enemy.hp < 10) {
    state.enemy.enraged = true;
    state.enemy.attack  = Math.floor(state.enemy.attack * 1.6);
    addLog(`🔥 ${state.enemy.name} entre en RAGE ! ATK → ${state.enemy.attack}`, 'rage');
  }
}

// ── Action du héros ───────────────────────────────────────
function heroAction(dir) {
  if (gameOver) return;

  turn++;
  document.getElementById('turn-counter').textContent = `Tour ${turn}`;

  // Déplacement
  let nx = state.hero.x;
  let ny = state.hero.y;
  if (dir === 'up')    ny = Math.max(0, ny - 1);
  if (dir === 'down')  ny = Math.min(BOARD_SIZE - 1, ny + 1);
  if (dir === 'left')  nx = Math.max(0, nx - 1);
  if (dir === 'right') nx = Math.min(BOARD_SIZE - 1, nx + 1);

  // Vérifie si la case cible est occupée par l'ennemi → attaque
  if (nx === state.enemy.x && ny === state.enemy.y && state.enemy.alive) {
    state.enemy.hp = Math.max(0, state.enemy.hp - state.hero.attack);
    addLog(`⚔ ${state.hero.name} frappe ${state.enemy.name} → ${state.hero.attack} dégâts !`, 'attack');

    if (state.enemy.hp <= 0) {
      state.enemy.alive = false;
      renderBoard();
      renderStats();
      endGame(true);
      return;
    }
  } else {
    // Déplacement normal
    state.hero.x = nx;
    state.hero.y = ny;
    addLog(`${state.hero.name} se déplace (${dir})`, 'move');

    // Potion ?
    if (!state.potion.collected &&
        state.hero.x === state.potion.x &&
        state.hero.y === state.potion.y) {
      state.potion.collected = true;
      const healed = Math.min(state.potion.amount, state.hero.maxHp - state.hero.hp);
      state.hero.hp += healed;
      addLog(`✨ ${state.hero.name} ramasse une potion ! +${healed} HP`, 'potion');
    }
  }

  // Tour ennemi
  enemyTurn();

  renderBoard();
  renderStats();
}

// ── Fin de partie ─────────────────────────────────────────
function endGame(victory) {
  gameOver = true;
  const msg = victory
    ? `🏆 Victoire ! ${state.hero.name} a vaincu ${state.enemy.name} en ${turn} tours !`
    : `💀 Défaite… ${state.hero.name} a été vaincu par ${state.enemy.name}.`;
  addLog(msg, 'end');
}

// ── Clavier ───────────────────────────────────────────────
document.addEventListener('keydown', e => {
  if (gameOver) return;
  const map = { z: 'up', s: 'down', q: 'left', d: 'right',
                ArrowUp: 'up', ArrowDown: 'down', ArrowLeft: 'left', ArrowRight: 'right' };
  const dir = map[e.key];
  if (dir) {
    e.preventDefault();
    heroAction(dir);
  }
});

// ── Init ──────────────────────────────────────────────────
addLog('Que la chronique commence… Utilise Z S Q D pour te déplacer.', 'move');
renderBoard();
renderStats();
</script>
