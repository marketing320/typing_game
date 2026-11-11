import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import test from 'node:test';
import vm from 'node:vm';

const source = await readFile(new URL('../../resources/js/arcade-ui.js', import.meta.url), 'utf8');

test('car earns distance only for correct characters and resets with the passage', () => {
    let characters = ['text-green-600', 'text-red-500', 'pending', 'pending'];
    let progress;
    let update;
    const display = {
        querySelectorAll: selector => selector === '.char'
            ? characters
            : characters.filter(state => selector.includes(state)),
    };
    const lane = { style: { setProperty: (name, value) => { progress = value; } } };

    vm.runInNewContext(source, {
        document: {
            getElementById: id => id === 'text-display' ? display : null,
            querySelector: selector => selector === '.race-lane' ? lane : null,
            querySelectorAll: () => [],
        },
        MutationObserver: class {
            constructor(callback) { update = callback; }
            observe() {}
        },
    });

    assert.equal(progress, '25%');
    characters[2] = 'text-red-500';
    update();
    assert.equal(progress, '25%');
    characters[3] = 'text-green-600';
    update();
    assert.equal(progress, '50%');
    characters = characters.map(() => 'pending');
    update();
    assert.equal(progress, '0%');
    characters = characters.map(() => 'text-green-600');
    update();
    assert.equal(progress, '100%');
    characters = [];
    update();
    assert.equal(progress, '0%');
});
