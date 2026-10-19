import fs from 'node:fs/promises';
import path from 'node:path';
import { pathToFileURL } from 'node:url';

const targets = await (await fetch('http://127.0.0.1:9223/json')).json();
const socket = new WebSocket(targets.find(target => target.type === 'page').webSocketDebuggerUrl);
await new Promise(resolve => socket.addEventListener('open', resolve, { once: true }));
let nextId = 0;
const pending = new Map();
socket.addEventListener('message', ({ data }) => {
    const message = JSON.parse(data);
    if (message.id && pending.has(message.id)) {
        const { resolve, reject } = pending.get(message.id);
        pending.delete(message.id);
        message.error ? reject(new Error(message.error.message)) : resolve(message.result);
    }
});
function command(method, params = {}) {
    return new Promise((resolve, reject) => {
        const id = ++nextId;
        pending.set(id, { resolve, reject });
        socket.send(JSON.stringify({ id, method, params }));
    });
}
async function evaluate(expression) {
    const response = await command('Runtime.evaluate', { expression, returnByValue: true, awaitPromise: true });
    if (response.exceptionDetails) throw new Error(response.exceptionDetails.text);
    return response.result.value;
}
await command('Page.enable');
const directory = path.resolve('storage/framework/ui-preview');
for (const width of [1440, 390]) {
    await command('Emulation.setDeviceMetricsOverride', { width, height: 960, deviceScaleFactor: 1, mobile: false });
    for (const page of ['home', 'practice', 'access', 'otp', 'play', 'result', 'leaderboard', 'login', 'admin']) {
        await command('Page.navigate', { url: pathToFileURL(path.join(directory, page + '.html')).href });
        for (let attempt = 0; attempt < 60; attempt++) {
            if (await evaluate('document.readyState === "complete"') && await evaluate('!!document.querySelector("main, .login-card")')) break;
            await new Promise(resolve => setTimeout(resolve, 250));
        }
        await evaluate('document.fonts.ready.then(() => true)');
        if (page === 'practice') await evaluate('dismissRules()');
        const metrics = await evaluate('({ width: innerWidth, content: document.documentElement.scrollWidth, heading: document.querySelector("h1")?.textContent.trim(), icons: document.querySelectorAll("svg.lucide").length })');
        console.log(JSON.stringify({ page, ...metrics }));
        const screenshot = await command('Page.captureScreenshot', { format: 'png', captureBeyondViewport: true });
        await fs.writeFile(path.join(directory, page + '-' + width + '.png'), Buffer.from(screenshot.data, 'base64'));
        if (width === 1440 && page === 'practice') {
            console.log('practice input:', await evaluate(`(() => { const el = document.getElementById('hidden-input'); el.value = FULL_TEXT[0]; el.dispatchEvent(new Event('input', { bubbles: true })); return { index: typedIndex, correct: document.querySelectorAll('.char.text-green-600').length, timerStarted: !!startTime }; })()`));
        }
        if (width === 1440 && page === 'leaderboard') {
            console.log('leaderboard update:', await evaluate(`(() => { applyUpdate({ entries: [{ rank: 1, username: 'new-player', score: 90, wpm: 90, accuracy: 100, duration_seconds: 10 }] }); return document.querySelector('#leaderboard-body tr').dataset.username; })()`));
        }
        if (width === 1440 && page === 'play') {
            console.log('profile controls:', await evaluate(`(() => { toggleCpDd({ stopPropagation() {} }); const open = !document.getElementById('cp-dd').classList.contains('hidden'); pickCountry('+65', 'SG'); return { open, country: document.getElementById('cp-code').textContent, referrals: document.querySelectorAll('[name=referral_source]').length }; })()`));
        }
    }
}
await command('Browser.close');
socket.close();
