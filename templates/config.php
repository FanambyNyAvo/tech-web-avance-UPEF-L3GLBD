<div class="config-screen">
  <p class="config-defaults">Modifie les stats ou lance avec les valeurs par défaut</p>
  <form method="POST">

    <!-- SÉLECTEUR DE MODE -->
    <div class="mode-selector">
      <div class="mode-card active" id="mode-simulation-card" onclick="selectMode('simulation')">
        <div class="mode-icon">⚙</div>
        <div class="mode-title">Simulation</div>
        <div class="mode-desc">L'IA contrôle les deux — avance tour par tour</div>
      </div>
      <div class="mode-card" id="mode-joueur-card" onclick="selectMode('joueur')">
        <div class="mode-icon">🎮</div>
        <div class="mode-title">Mode Joueur</div>
        <div class="mode-desc">Tu contrôles le héros avec Z S Q D</div>
      </div>
    </div>
    <input type="hidden" name="mode" id="mode-input" value="simulation">

    <!-- STATS -->
    <div class="config-grid">
      <div class="config-card">
        <div class="config-card-title">⚔ Aldric — Héros</div>
        <div class="config-field">
          <label>POINTS DE VIE</label>
          <input type="number" name="hero_hp" value="30" min="1" max="100">
          <div class="config-hint">1 — 100</div>
        </div>
        <div class="config-field">
          <label>ATTAQUE</label>
          <input type="number" name="hero_atk" value="7" min="1" max="50">
          <div class="config-hint">1 — 50</div>
        </div>
      </div>
      <div class="config-card enemy">
        <div class="config-card-title">💀 Gobelin — Ennemi</div>
        <div class="config-field">
          <label>POINTS DE VIE</label>
          <input type="number" name="enemy_hp" value="20" min="1" max="100">
          <div class="config-hint">1 — 100</div>
        </div>
        <div class="config-field">
          <label>ATTAQUE</label>
          <input type="number" name="enemy_atk" value="5" min="1" max="50">
          <div class="config-hint">1 — 50</div>
        </div>
      </div>
    </div>

    <div class="btn-row">
      <button type="submit" name="lancer" class="btn">⚔ Lancer la bataille</button>
    </div>
  </form>
</div>

<script>
function selectMode(mode) {
  document.getElementById('mode-input').value = mode;
  document.getElementById('mode-simulation-card').classList.toggle('active', mode === 'simulation');
  document.getElementById('mode-joueur-card').classList.toggle('active', mode === 'joueur');
}
</script>
