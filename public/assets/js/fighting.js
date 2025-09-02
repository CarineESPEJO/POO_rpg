const godzillaFight = document.getElementById("godzilla_fight");
const kongFight = document.getElementById("kong_fight");
const godzillaHeal = document.getElementById("godzilla_heal");
const kongHeal = document.getElementById("kong_heal");
const newFight = document.getElementById("new_fight");
const winnerElem = document.getElementById("winner_message");

let currentTurn = "godzilla"; // Godzilla starts

// --- Update stats on the page ---
function updateStats(data) {
    document.getElementById("godzilla_life").textContent = data.godzilla.life;
    document.getElementById("godzilla_strength").textContent = data.godzilla.strength || 40;
    document.getElementById("godzilla_intel").textContent = data.godzilla.intelligence;
    document.getElementById("godzilla_stamina").textContent = data.godzilla.stamina;

    document.getElementById("kong_life").textContent = data.kong.life;
    document.getElementById("kong_strength").textContent = data.kong.strength || 60;
    document.getElementById("kong_intel").textContent = data.kong.intelligence;
    document.getElementById("kong_stamina").textContent = data.kong.stamina;

    // Disable heal if life is 100 or not player's turn
    godzillaHeal.disabled = (data.godzilla.life >= 100 || currentTurn !== "godzilla");
    kongHeal.disabled = (data.kong.life >= 100 || currentTurn !== "kong");
}

//Enable only current player's buttons
function setTurn(turn) {
    currentTurn = turn;
    if (turn === "godzilla") {
        godzillaFight.disabled = false;
        godzillaHeal.disabled = (parseInt(document.getElementById("godzilla_life").textContent) >= 100);
        kongFight.disabled = true;
        kongHeal.disabled = true;
    } else {
        godzillaFight.disabled = true;
        godzillaHeal.disabled = true;
        kongFight.disabled = false;
        kongHeal.disabled = (parseInt(document.getElementById("kong_life").textContent) >= 100);
    }
}

// Disable all buttons (game over)
function disableButtons() {
    godzillaFight.disabled = true;
    kongFight.disabled = true;
    godzillaHeal.disabled = true;
    kongHeal.disabled = true;
}

//Check for winner
function checkWinner(data) {
    if ((data.godzilla.life <= 0 && data.kong.life <= 0) || 
        (data.godzilla.stamina < 15 && data.kong.stamina < 15 && data.godzilla.life > 0 && data.kong.life > 0)) {
        winnerElem.textContent = "It's a draw!";
        disableButtons();
    } else if (data.godzilla.life <= 0) {
        winnerElem.textContent = "Kong wins!";
        disableButtons();
    } else if (data.kong.life <= 0) {
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


godzillaFight.addEventListener("click", () => action('fight', 'godzilla'));
kongFight.addEventListener("click", () => action('fight', 'kong'));
godzillaHeal.addEventListener("click", () => action('heal', 'godzilla'));
kongHeal.addEventListener("click", () => action('heal', 'kong'));
newFight.addEventListener("click", () => action('reset'));

// Initialize first turn
setTurn("godzilla");
winnerElem.textContent = "Godzilla's turn!";
