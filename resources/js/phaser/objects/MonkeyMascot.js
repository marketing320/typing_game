import Phaser from 'phaser';

export default class MonkeyMascot extends Phaser.GameObjects.Container {
    constructor(scene, x, y, state = 'idle') {
        super(scene, x, y);

        // Body (brown circle)
        this.body_ = scene.add.graphics();
        this.body_.fillStyle(0x92400e, 1);
        this.body_.fillCircle(0, 0, 22);

        // Belly (lighter)
        this.belly = scene.add.graphics();
        this.belly.fillStyle(0xfcd34d, 0.8);
        this.belly.fillEllipse(0, 6, 20, 16);

        // Eyes
        this.leftEye = scene.add.graphics();
        this.leftEye.fillStyle(0xffffff, 1);
        this.leftEye.fillCircle(-8, -6, 6);
        this.leftEye.fillStyle(0x1c1917, 1);
        this.leftEye.fillCircle(-8, -6, 3);

        this.rightEye = scene.add.graphics();
        this.rightEye.fillStyle(0xffffff, 1);
        this.rightEye.fillCircle(8, -6, 6);
        this.rightEye.fillStyle(0x1c1917, 1);
        this.rightEye.fillCircle(8, -6, 3);

        // Mouth
        this.mouth = scene.add.graphics();
        this.mouth.lineStyle(2, 0x1c1917, 1);
        this.mouth.beginPath();
        this.mouth.arc(0, 8, 8, 0.2, Math.PI - 0.2);
        this.mouth.strokePath();

        // Ears
        this.leftEar = scene.add.graphics();
        this.leftEar.fillStyle(0x92400e, 1);
        this.leftEar.fillCircle(-22, -10, 8);
        this.leftEar.fillStyle(0xfcd34d, 0.7);
        this.leftEar.fillCircle(-22, -10, 4);

        this.rightEar = scene.add.graphics();
        this.rightEar.fillStyle(0x92400e, 1);
        this.rightEar.fillCircle(22, -10, 8);
        this.rightEar.fillStyle(0xfcd34d, 0.7);
        this.rightEar.fillCircle(22, -10, 4);

        this.add([this.body_, this.belly, this.leftEar, this.rightEar, this.leftEye, this.rightEye, this.mouth]);
        scene.add.existing(this);

        // Idle bob animation
        scene.tweens.add({
            targets: this,
            y: y - 5,
            duration: 800,
            yoyo: true,
            repeat: -1,
            ease: 'Sine.easeInOut',
        });
    }

    playCorrect() {
        this.scene.tweens.add({
            targets: this,
            scaleX: 1.2, scaleY: 1.2,
            duration: 150,
            yoyo: true,
            ease: 'Back.easeOut',
        });
        // Green flash
        this.mouth.clear();
        this.mouth.fillStyle(0x22c55e, 1);
        this.mouth.fillCircle(0, 10, 7);
        this.scene.time.delayedCall(300, () => this._resetMouth());
    }

    playError() {
        this.scene.cameras.main.shake(80, 0.01);
        this.scene.tweens.add({
            targets: this,
            tint: 0xff4444,
            duration: 200,
            yoyo: true,
        });
        // Shocked mouth
        this.mouth.clear();
        this.mouth.fillStyle(0x1c1917, 1);
        this.mouth.fillCircle(0, 10, 6);
        this.scene.time.delayedCall(400, () => this._resetMouth());
    }

    _resetMouth() {
        this.mouth.clear();
        this.mouth.lineStyle(2, 0x1c1917, 1);
        this.mouth.beginPath();
        this.mouth.arc(0, 8, 8, 0.2, Math.PI - 0.2);
        this.mouth.strokePath();
    }
}
