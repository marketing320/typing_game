import Phaser from 'phaser';

export default class TypingTextDisplay extends Phaser.GameObjects.Container {
    constructor(scene, x, y, width, text) {
        super(scene, x, y);
        this.displayWidth = width;
        this.charObjects = [];

        const chars = text.split('');
        let curX = 0;
        let curY = 0;
        const charW = 11;
        const lineH = 22;
        const maxW = width;

        chars.forEach((ch, i) => {
            if (curX + charW > maxW) {
                curX = 0;
                curY += lineH;
            }

            const t = scene.add.text(curX, curY, ch === ' ' ? ' ' : ch, {
                fontSize: '14px',
                color: '#9ca3af',
                fontFamily: 'monospace',
            });

            this.charObjects.push(t);
            this.add(t);

            curX += ch === ' ' ? 7 : charW;
        });

        scene.add.existing(this);
    }

    highlightChar(index, state) {
        const obj = this.charObjects[index];
        if (!obj) return;
        const colors = {
            correct: '#4ade80',
            wrong: '#f87171',
            current: '#fbbf24',
            pending: '#9ca3af',
        };
        obj.setColor(colors[state] || '#9ca3af');
    }
}
