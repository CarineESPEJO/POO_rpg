// /assets/js/fighting.js
const characters = Array.from(document.querySelectorAll(".character-block"));
const winnerElem = document.getElementById("winner_message");
const actionElem = document.getElementById("action_message");
const historyElem = document.getElementById("history");
const newFight = document.getElementById("new_fight");

let currentTurn = "char1"; // char1 starts
const MIN_STATS = 0;
const HEAL_THRESHOLD = 10;

// helper to create buttons
function createButton(id, label, onClick) {
    const btn = document.createElement("button");
    btn.id = id;
    btn.textContent = label;
    btn.addEventListener("click", onClick);
    return btn;
}

// build controls dynamically based on data-class attribute
function buildControls() {
    characters.forEach(char => {
        const id = char.id;
        const cls = char.dataset.class;
        const name = char.dataset.name;
        const controls = document.getElementById(`${id}_controls`);
        controls.innerHTML = ""; // reset (safe if re-called)

        // Attack
        controls.appendChild(createButton(`${id}_attack`, `${name} Attack`, () => action('attack', id)));
        // Heal
        controls.appendChild(createButton(`${id}_heal`, `${name} Heal`, () => action('heal', id)));

        // Class-specific specials
        if (cls === "Warrior") {
            controls.appendChild(createButton(`${id}_powerstrike`, `${name} Power Strike`, () => action('powerstrike', id)));
        } else if (cls === "Assassin") {
            controls.appendChild(createButton(`${id}_sneakattack`, `${name} Sneak Attack`, () => action('sneakattack', id)));
        } // extend here for more subclasses
    });
}

// update DOM stat fields and show message + push to history
function updateStats(data) {
    // update char stats
    ['char1','char2'].forEach(id => {
        if (!data[id]) return;
        document.getElementById(`${id}_health`).textContent = data[id].health;
        document.getElementById(`${id}_strength`).textContent = data[id].strength;
        document.getElementById(`${id}_intel`).textContent = data[id].intelligence;
        document.getElementById(`${id}_stamina`).textContent = data[id].stamina;

        // also sync dataset name/class if backend provided (optional)
        const block = document.getElementById(id);
        if (data[id].name) block.dataset.name = data[id].name;
        if (data[id].class) block.dataset.class = data[id].class;
    });

    // show current action message (single line or multi-line)
    actionElem.textContent = data.message || "";

    // update history (prepend newest)
    if (data.message) {
        const p = document.createElement("p");
        p.textContent = data.message;
        historyElem.prepend(p);
    }

    // re-evaluate button enabling based on currentTurn and stat thresholds
    setTurn(currentTurn);
}

// enable only the current player's buttons and disable the other
function setTurn(turn) {
    currentTurn = turn;
    characters.forEach(char => {
        const id = char.id;
        const controls = document.getElementById(`${id}_controls`);
        Array.from(controls.children).forEach(btn => {
            // base enable for player's controls only
            btn.disabled = (id !== currentTurn);
        });

        // additionally enforce stamina/intelligence thresholds for the enabled player's buttons
        if (id === currentTurn) {
            const stamina = parseInt(document.getElementById(`${id}_stamina`).textContent, 10);
            const intel = parseInt(document.getElementById(`${id}_intel`).textContent, 10);
            // attack requires stamina >= 15
            const attackBtn = document.getElementById(`${id}_attack`);
            if (attackBtn) attackBtn.disabled = (stamina < 15);
            // powerstrike requires stamina >= 30
            const powerBtn = document.getElementById(`${id}_powerstrike`);
            if (powerBtn) powerBtn.disabled = (stamina < 30);
            // sneakattack requires stamina >= 40
            const sneakBtn = document.getElementById(`${id}_sneakattack`);
            if (sneakBtn) sneakBtn.disabled = (stamina < 40);
            // heal requires intelligence >= HEAL_THRESHOLD and health < 100
            const healBtn = document.getElementById(`${id}_heal`);
            if (healBtn) {
                const health = parseInt(document.getElementById(`${id}_health`).textContent, 10);
                healBtn.disabled = (intel < HEAL_THRESHOLD || health >= 100);
            }
        }
    });
}

// disable everything (game over)
function disableButtons() {
    characters.forEach(char => {
        const controls = document.getElementById(`${char.id}_controls`);
        Array.from(controls.children).forEach(btn => btn.disabled = true);
    });
}

// check winner after each action and switch turn if game continues
function checkWinner(data) {
    const c1 = data.char1;
    const c2 = data.char2;

    // draw: both HP 0 or both stamina <15 while still alive
    if ((c1.health <= MIN_STATS && c2.health <= MIN_STATS) ||
        (c1.stamina < 15 && c2.stamina < 15 && c1.health > 0 && c2.health > 0)) {
        winnerElem.textContent = "It's a draw!";
        actionElem.textContent = "";
        disableButtons();
        return;
    }

    // check each losing condition (hp <=0 or intelligence/heal/stamina exhaustion)
    if (c1.health <= 0 || (c1.intelligence < HEAL_THRESHOLD && c1.stamina < 15)) {
        winnerElem.textContent = `${document.getElementById('char2').dataset.name} wins!`;
        actionElem.textContent = "";
        disableButtons();
        return;
    }

    if (c2.health <= 0 || (c2.intelligence < HEAL_THRESHOLD && c2.stamina < 15)) {
        winnerElem.textContent = `${document.getElementById('char1').dataset.name} wins!`;
        actionElem.textContent = "";
        disableButtons();
        return;
    }

    // otherwise switch turn and clear action message
    currentTurn = (currentTurn === 'char1') ? 'char2' : 'char1';
    winnerElem.textContent = `${document.getElementById(currentTurn).dataset.name}'s turn!`;
    actionElem.textContent = "";
    setTurn(currentTurn);
}

// call backend
function action(type, player) {
    const formData = new URLSearchParams();
    formData.append('action', type);
    formData.append('player', player);

    fetch('fightingAction.php', { method: 'POST', body: formData })
        .then(res => {
            if (!res.ok) {
                // server responded with non-2xx — try to read text to show reason
                return res.text().then(txt => { throw new Error('Server error: ' + txt); });
            }
            return res.json();
        })
        .then(data => {
            if (data.error) throw new Error(data.error);
            if (!data.char1 || !data.char2) throw new Error('Invalid server response.');

            updateStats(data);

            if (type !== 'reset') {
                checkWinner(data);
            } else {
                // reset: set turn to char1 and clear history
                currentTurn = 'char1';
                winnerElem.textContent = `${document.getElementById('char1').dataset.name}'s turn!`;
                actionElem.textContent = '';
                historyElem.innerHTML = '';
                setTurn('char1');
            }
        })
        .catch(err => {
            console.error('Action error:', err);
            // show a short message so user knows something happened
            actionElem.textContent = 'Error: ' + err.message;
        });
}

// wire new fight button
newFight.addEventListener('click', () => action('reset', 'char1'));

// initial setup
buildControls();
setTurn(currentTurn);
winnerElem.textContent = `${document.getElementById(currentTurn).dataset.name}'s turn!`;
actionElem.textContent = "";
