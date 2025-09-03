const characters = Array.from(document.querySelectorAll(".character-card"));
const winnerElem = document.getElementById("winner_message");
const actionElem = document.getElementById("action_message");
const historyElem = document.getElementById("history");
const newFight = document.getElementById("new_fight");

let currentTurn = 'char1';

// Create a button helper
function createButton(id, label, onClick) {
    const btn = document.createElement("button");
    btn.id = id;
    btn.textContent = label;
    btn.addEventListener("click", onClick);
    return btn;
}

// Build the control buttons for each character
function buildControls() {
    characters.forEach(char => {
        const id = char.id;
        const controls = document.getElementById(`${id}_controls`);
        controls.innerHTML = '';
        controls.appendChild(createButton(`${id}_attack`, 'Attack', () => action('attack', id)));
        controls.appendChild(createButton(`${id}_heal`, 'Heal', () => action('heal', id)));
        // Change this line:
        controls.appendChild(createButton(`${id}_ability`, char.dataset.ability, () => action('useAbility', id)));
        controls.appendChild(createButton(`${id}_inspect`, 'Inspect', () => action('inspect', id)));
    });
}


// Update character stats and history
function updateStats(data) {
    ['char1','char2'].forEach(id => {
        const charData = data[id];
        if (!charData) return;
        document.getElementById(`${id}_health`).textContent = charData.health;
        document.getElementById(`${id}_strength`).textContent = charData.strength;
        document.getElementById(`${id}_intel`).textContent = charData.intelligence;
        document.getElementById(`${id}_stamina`).textContent = charData.stamina;
        document.getElementById(id).dataset.name = charData.name;
        document.getElementById(id).dataset.ability = charData.abilityName;
    });

    if (data.message) {
        actionElem.textContent = data.message;
        const p = document.createElement('p');
        p.textContent = data.message;
        historyElem.prepend(p);
    }

    if (data.winner) {
        winnerElem.textContent = data.winner;
        disableAllButtons();
    } else {
        currentTurn = data.currentTurn || currentTurn;
        winnerElem.textContent = `${document.getElementById(currentTurn).dataset.name}'s turn!`;
        setTurn(currentTurn);
    }
}

// Enable only current turn buttons
function setTurn(turn) {
    characters.forEach(char => {
        const controls = document.getElementById(`${char.id}_controls`);
        Array.from(controls.children).forEach(btn => {
            if (btn.id.endsWith('attack') || btn.id.endsWith('heal') || btn.id.endsWith('ability')) {
                btn.disabled = (char.id !== turn);
            } else {
                btn.disabled = false; // Inspect always enabled
            }
        });
    });
}

// Disable all action buttons
function disableAllButtons() {
    characters.forEach(char => {
        const controls = document.getElementById(`${char.id}_controls`);
        Array.from(controls.children).forEach(btn => btn.disabled = true);
    });
}

// Perform an action via fetch
function action(type, player) {
    const formData = new URLSearchParams();
    formData.append('action', type);
    formData.append('player', player);

    fetch('fightingAction.php', { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
            if (data.error) throw new Error(data.error);
            updateStats(data);

            if (type === 'reset') {
                historyElem.innerHTML = '';
                currentTurn = 'char1';
                winnerElem.textContent = `${document.getElementById(currentTurn).dataset.name}'s turn!`;
                actionElem.textContent = '';
            }
        })
        .catch(err => {
            console.error(err);
            actionElem.textContent = 'Error: ' + err.message;
        });
}

// New fight button
newFight.addEventListener('click', () => action('reset', 'char1'));

// Initial setup
buildControls();
setTurn(currentTurn);
winnerElem.textContent = `${document.getElementById(currentTurn).dataset.name}'s turn!`;
actionElem.textContent = '';
