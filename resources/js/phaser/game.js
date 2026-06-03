import Phaser from 'phaser';
import BootScene from './scenes/BootScene.js';
import PreloadScene from './scenes/PreloadScene.js';
import MenuScene from './scenes/MenuScene.js';
import TypingScene from './scenes/TypingScene.js';
import ResultScene from './scenes/ResultScene.js';

const config = {
    type: Phaser.AUTO,
    parent: 'phaser-game',
    width: 480,
    height: 320,
    backgroundColor: '#78350f',
    scene: [BootScene, PreloadScene, MenuScene, TypingScene, ResultScene],
    scale: {
        mode: Phaser.Scale.FIT,
        autoCenter: Phaser.Scale.CENTER_BOTH,
    },
    physics: { default: 'arcade' },
};

export default function createGame() {
    return new Phaser.Game(config);
}
