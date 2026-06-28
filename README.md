# PROJET - 3EME ANNEE - TECH WEB AVANCE - UPEF #

Projet semestriel — Tech Web Avancé (L3 GLBD)

---

## Lancer le projet

1. Copier le dossier dans `C:\xampp\htdocs\tech-web-avance-UPEF-L3GLBD\`
2. Démarrer **Apache** dans XAMPP
3. Ouvrir `http://localhost/tech-web-avance-UPEF-L3GLBD/index.php`

---

## Structure

```
mini-rpg/
├── index.php                  ← point d'entrée et contrôleur
├── classes/
│   ├── Character.php          ← classe abstraite (nom, hp, attack, x, y)
│   ├── Hero.php               ← héros Aldric + méthode heal()
│   ├── Enemy.php              ← ennemi Gobelin + mécanique rage
│   ├── Potion.php             ← collectible posé sur le plateau
│   ├── Board.php              ← grille 10×10 + calcul de distance
│   └── Game.php               ← simulation complète → snapshots JSON
├── templates/
│   ├── layout.php             ← squelette HTML, routing des vues
│   ├── config.php             ← formulaire de stats + choix du mode
│   ├── game_simulation.php    ← arène mode simulation + JavaScript
│   └── game_joueur.php        ← arène mode joueur + contrôles clavier
└── assets/
    ├── style.css              ← thème RPG fantasy (parchemin, tons dorés)
    ├── mode.css               ← CSS sélecteur de mode + touches clavier
    ├── hero.png               ← image du héros
    ├── enemy.png              ← image de l'ennemi
    └── potion.png             ← image de la potion
```

---

## Modes de jeu

### Mode Simulation
La partie est entièrement calculée par PHP puis rejouée tour par tour dans le navigateur.
- Cliquer sur **Tour suivant** pour avancer
- Le héros et l'ennemi sont contrôlés par l'IA

### Mode Joueur
Le joueur contrôle directement le héros en temps réel.
- **Z** → Haut
- **S** → Bas
- **Q** → Gauche
- **D** → Droite
- L'attaque se déclenche **automatiquement** quand le héros marche sur la case de l'ennemi
- L'ennemi se déplace à chaque tour du joueur

---

## Comment jouer

1. Configurer les Vies et Attaque du héros et de l'ennemi
2. Choisir le mode — **Simulation** ou **Mode Joueur**
3. Cliquer sur **Lancer la bataille**
4. À la fin, **Reconfigurer** pour rejouer

---

## Mécaniques bonus

- Le héros **fuit vers la potion** si ses HP tombent à 50%
- L'ennemi entre en **RAGE** (ATK ×1.6) si ses HP passent sous 10
- Les positions de départ sont **aléatoires** à chaque partie
- La potion est un **collectible physique** sur le plateau — pas un simple bouton

---

## Architecture des classes

```
Character (abstraite)
├── Hero     → heal(amount) : récupère des HP via potion
└── Enemy    → checkRage()  : boost ATK si HP < 10

Potion      → isAtPosition(), collect()
Board       → render(), distance(), isValid()
Game        → simulate() → tableau de snapshots JSON
```

---

## Technologies utilisées

| Couche | Technologie |
|---|---|
| Logique métier | PHP 8.x (POO) |
| Affichage | HTML5 / CSS3 |
| Interactivité | JavaScript vanilla |
| Serveur local | XAMPP / Apache |

---

## Auteur

Fanamby Ny Avo — L3 GLBD - UPEF - 2026
