// Main app entry — exposes Phaser game factory for pages that need it
import createGame from './phaser/game.js';

window.createTypingMonkeyGame = createGame;
