import Phaser from 'phaser';

export default class BananaProgressBar extends Phaser.GameObjects.Container {
    constructor(scene, x, y, width) {
        super(scene, x, y);
        this.barWidth = width;

        // Track
        this.track = scene.add.graphics();
        this.track.fillStyle(0x451a03, 0.8);
        this.track.fillRoundedRect(0, 0, width, 16, 8);

        // Fill
        this.fill = scene.add.graphics();

        // Banana icon at the end
        this.banana = scene.add.text(width + 4, -4, '🍌', { fontSize: '18px' });

        this.add([this.track, this.fill, this.banana]);
        scene.add.existing(this);
        this.setProgress(0);
    }

    setProgress(pct) {
        const w = Math.max(4, this.barWidth * Math.min(1, pct));
        this.fill.clear();
        this.fill.fillStyle(0xfbbf24, 1);
        this.fill.fillRoundedRect(0, 2, w, 12, 6);
    }
}
