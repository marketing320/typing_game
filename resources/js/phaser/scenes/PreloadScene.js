import Phaser from 'phaser';

export default class PreloadScene extends Phaser.Scene {
    constructor() { super({ key: 'PreloadScene' }); }

    preload() {
        // No external assets required — all graphics drawn with Phaser shapes
    }

    create() {
        this.scene.start('MenuScene');
    }
}
