import Phaser from 'phaser';
import MonkeyMascot from '../objects/MonkeyMascot.js';

export default class MenuScene extends Phaser.Scene {
    constructor() { super({ key: 'MenuScene' }); }

    create() {
        const { width, height } = this.scale;

        // Sky gradient background
        const bg = this.add.graphics();
        bg.fillGradientStyle(0x78350f, 0x78350f, 0x92400e, 0xa16207, 1);
        bg.fillRect(0, 0, width, height);

        // Floating leaves
        for (let i = 0; i < 8; i++) {
            const leaf = this.add.graphics();
            leaf.fillStyle(0x16a34a, 0.6);
            leaf.fillEllipse(0, 0, 20, 10);
            leaf.x = Phaser.Math.Between(20, width - 20);
            leaf.y = Phaser.Math.Between(20, height - 20);
            this.tweens.add({ targets: leaf, y: leaf.y - 30, duration: 2000 + i * 400, yoyo: true, repeat: -1, ease: 'Sine.easeInOut' });
        }

        // Title panel (wooden plank look)
        const panel = this.add.graphics();
        panel.fillStyle(0x92400e, 1);
        panel.fillRoundedRect(width / 2 - 130, 20, 260, 50, 10);
        panel.lineStyle(3, 0xd97706, 1);
        panel.strokeRoundedRect(width / 2 - 130, 20, 260, 50, 10);

        this.add.text(width / 2, 45, '🐒 Typing Monkey', {
            fontSize: '20px', color: '#fef3c7', fontStyle: 'bold',
        }).setOrigin(0.5);

        // Monkey mascot
        this.monkey = new MonkeyMascot(this, width / 2, 160, 'idle');

        // Banana progress bar teaser
        this.add.text(width / 2, height - 40, 'Tap / Click to Start', {
            fontSize: '14px', color: '#fde68a',
        }).setOrigin(0.5);

        this.input.on('pointerdown', () => this.scene.start('TypingScene'));
        this.input.keyboard?.on('keydown', () => this.scene.start('TypingScene'));
    }
}
