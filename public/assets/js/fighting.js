const godzillaAttack = document.getElementById("godzilla_attack");
const kongAttack = document.getElementById("kong_attack");
const godzillaHeal = document.getElementById("godzilla_heal");
const kongHeal = document.getElementById("kong_heal");
const newFight = document.getElementById("new_fight");
const winnerElem = document.getElementById("winner_message");

let currentTurn = "godzilla"; // Godzilla starts

const MIN_STATS = 0;
const MAX_STATS = 100;
const HEAL_THRESHOLD = 10;  

// --- Update stats on the page ---
function updateStats(data) {
    document.getElementById("godzilla_health").textContent = data.godzilla.health;
    document.getElementById("godzilla_strength").textContent = data.godzilla.strength || 40;
    document.getElementById("godzilla_intel").textContent = data.godzilla.intelligence;
    document.getElementById("godzilla_stamina").textContent = data.godzilla.stamina;

    document.getElementById("kong_health").textContent = data.kong.health;
    document.getElementById("kong_strength").textContent = data.kong.strength || 60;
    document.getElementById("kong_intel").textContent = data.kong.intelligence;
    document.getElementById("kong_stamina").textContent = data.kong.stamina;

    // Disable heal if intelligence is below PHP threshold or health is max or not player's turn
    
    godzillaHeal.disabled = (data.godzilla.intelligence < HEAL_THRESHOLD || data.godzilla.health >= MAX_STATS || currentTurn !== "godzilla");
    kongHeal.disabled = (data.kong.intelligence < HEAL_THRESHOLD || data.kong.health >= MAX_STATS || currentTurn !== "kong");
    // Disable New Fight button if both health and stamina are maxed
    newFight.disabled = (
        data.godzilla.health === MAX_STATS && data.godzilla.stamina === MAX_STATS &&
        data.kong.health === MAX_STATS && data.kong.stamina === MAX_STATS
    );
}



//Enable only current player's buttons
function setTurn(turn) {
    currentTurn = turn;
    if (turn === "godzilla") {
        godzillaAttack.disabled =  (parseInt(document.getElementById("godzilla_stamina").textContent) < 15);
        godzillaHeal.disabled = (parseInt(document.getElementById("godzilla_health").textContent) >= MAX_STATS || parseInt(document.getElementById("godzilla_intel").textContent) < HEAL_THRESHOLD);
        kongAttack.disabled = true;
        kongHeal.disabled = true;
    } else {
        godzillaAttack.disabled = true;
        godzillaHeal.disabled = true;
        kongAttack.disabled = (parseInt(document.getElementById("kong_stamina").textContent) < 15);
        kongHeal.disabled = (parseInt(document.getElementById("kong_health").textContent) >= MAX_STATS || parseInt(document.getElementById("kong_intel").textContent) < HEAL_THRESHOLD);
    }
}

// Disable all buttons (game over)
function disableButtons() {
    godzillaAttack.disabled = true;
    kongAttack.disabled = true;
    godzillaHeal.disabled = true;
    kongHeal.disabled = true;
}

//Check for winner
function checkWinner(data) {
    if ((data.godzilla.health <= MIN_STATS && data.kong.health <= MIN_STATS) || 
        (data.godzilla.stamina < 15 && data.kong.stamina < 15 && data.godzilla.health > 0 && data.kong.health > 0)) {
        winnerElem.textContent = "It's a draw!";
        disableButtons();
    } else if (data.godzilla.health <= 0 || (data.godzilla.intelligence < HEAL_THRESHOLD && data.godzilla.stamina < 15)) {
        winnerElem.textContent = "Kong wins!";
        disableButtons();
    } else if (data.kong.health <= 0 || (data.kong.intelligence < HEAL_THRESHOLD && data.kong.stamina < 15)) {
        winnerElem.textContent = "Godzilla wins!";
        disableButtons();
    } else {
        // Switch turn
        setTurn(currentTurn === "godzilla" ? "kong" : "godzilla");
        winnerElem.textContent = (currentTurn === "godzilla") ? "Godzilla's turn!" : "Kong's turn!";
    }
}

// Call fightingActions.php
function action(type, player = null) {
    const formData = new URLSearchParams();
    formData.append('action', type);
    if (player) formData.append('player', player);

    fetch("fightingAction.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            updateStats(data);
            if(type !== 'reset') checkWinner(data);
            else setTurn('godzilla'); // reset turn
        })
        .catch(err => console.error(err));
}


godzillaAttack.addEventListener("click", () => action('attack', 'godzilla'));
kongAttack.addEventListener("click", () => action('attack', 'kong'));
godzillaHeal.addEventListener("click", () => action('heal', 'godzilla'));
kongHeal.addEventListener("click", () => action('heal', 'kong'));
newFight.addEventListener("click", () => action('reset'));

// Initialize first turn
setTurn("godzilla");
winnerElem.textContent = "Godzilla's turn!";
