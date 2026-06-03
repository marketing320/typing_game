import Phaser from 'phaser';
import MonkeyMascot from '../objects/MonkeyMascot.js';
import BananaProgressBar from '../objects/BananaProgressBar.js';
import TypingTextDisplay from '../objects/TypingTextDisplay.js';

export default class TypingScene extends Phaser.Scene {
    constructor() { super({ key: 'TypingScene' }); }

    init(data) {
        this.gameText = data.text || '';
        this.onCharTyped = data.onCharTyped || null;
        this.onComplete = data.onComplete || null;
    }

    create() {
        const { width, height } = this.scale;

        // Background
        const bg = this.add.graphics();
        bg.fillGradientStyle(0x78350f, 0x78350f, 0x92400e, 0xa16207, 1);
        bg.fillRect(0, 0, width, height);

        // Monkey mascot top-right
        this.monkey = new MonkeyMascot(this, width - 60, 60, 'idle');

        // Banana progress bar
        this.progressBar = new BananaProgressBar(this, 10, height - 30, width - 20);

        // Stats display
        this.wpmText = this.add.text(10, 10, 'WPM: 0', { fontSize: '12px', color: '#fde68a' });
        this.accText = this.add.text(10, 26, 'ACC: 100%', { fontSize: '12px', color: '#86efac' });
        this.timeText = this.add.text(width / 2, 10, '0s', { fontSize: '12px', color: '#93c5fd' }).setOrigin(0.5, 0);

        // Text display area (wooden panel)
        const panelY = 50;
        const panel = this.add.graphics();
        panel.fillStyle(0x451a03, 0.85);
        panel.fillRoundedRect(8, panelY, width - 16, height - panelY - 50, 8);
        panel.lineStyle(2, 0xd97706, 0.7);
        panel.strokeRoundedRect(8, panelY, width - 16, height - panelY - 50, 8);

        this.textDisplay = new TypingTextDisplay(this, 16, panelY + 10, width - 32, this.gameText);
    }

    reactToChar(correct) {
        if (correct) {
            this.monkey.playCorrect();
        } else {
            this.monkey.playError();
        }
    }

    updateStats(wpm, accuracy, elapsed, progress) {
        this.wpmText.setText('WPM: ' + wpm);
        this.accText.setText('ACC: ' + accuracy + '%');
        this.timeText.setText(elapsed + 's');
        this.progressBar.setProgress(progress);
    }

    highlightChar(index, state) {
        this.textDisplay.highlightChar(index, state);
    }
}
