// Main app entry — exposes Phaser game factory for pages that need it
import createGame from './phaser/game.js';
import './arcade-ui.js';

window.createTypingMonkeyGame = createGame;
