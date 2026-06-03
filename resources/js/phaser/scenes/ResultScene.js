import Phaser from 'phaser';
import MonkeyMascot from '../objects/MonkeyMascot.js';

export default class ResultScene extends Phaser.Scene {
    constructor() { super({ key: 'ResultScene' }); }

    init(data) {
        this.wpm = data.wpm || 0;
        this.accuracy = data.accuracy || 0;
        this.duration = data.duration || 0;
    }

    create() {
        const { width, height } = this.scale;

        // Background
        const bg = this.add.graphics();
        bg.fillGradientStyle(0x14532d, 0x14532d, 0x166534, 0x15803d, 1);
        bg.fillRect(0, 0, width, height);

        // Celebration stars
        for (let i = 0; i < 12; i++) {
            const star = this.add.text(
                Phaser.Math.Between(20, width - 20),
                Phaser.Math.Between(20, height - 20),
                '⭐',
                { fontSize: '16px' }
            );
            this.tweens.add({
                targets: star,
                scaleX: { from: 0, to: 1 },
                scaleY: { from: 0, to: 1 },
                alpha: { from: 0, to: 1 },
                delay: i * 80,
                duration: 400,
                ease: 'Back.easeOut',
            });
        }

        // Monkey celebrating
        const monkey = new MonkeyMascot(this, width / 2, height / 2 - 60, 'celebrate');

        // Result panel
        const panel = this.add.graphics();
        panel.fillStyle(0x000000, 0.5);
        panel.fillRoundedRect(width / 2 - 110, height / 2 - 20, 220, 110, 12);

        this.add.text(width / 2, height / 2, `${this.wpm} WPM`, {
            fontSize: '32px', color: '#fde68a', fontStyle: 'bold',
        }).setOrigin(0.5);

        this.add.text(width / 2, height / 2 + 40, `Accuracy: ${this.accuracy}%`, {
            fontSize: '16px', color: '#86efac',
        }).setOrigin(0.5);

        this.add.text(width / 2, height / 2 + 65, `Time: ${this.duration}s`, {
            fontSize: '13px', color: '#93c5fd',
        }).setOrigin(0.5);
    }
}
