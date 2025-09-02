const godzillaAttack = document.getElementById("godzilla_attack");
const godzillaPower = document.getElementById("godzilla_powerstrike");
const godzillaHeal = document.getElementById("godzilla_heal");

const kongAttack = document.getElementById("kong_attack");
const kongSneak = document.getElementById("kong_sneakattack");
const kongHeal = document.getElementById("kong_heal");

const newFight = document.getElementById("new_fight");
const winnerElem = document.getElementById("winner_message");
const actionElem = document.getElementById("action_message");
const historyElem = document.getElementById("action_history");

let currentTurn = "godzilla";
const MIN_STATS = 0;
const MAX_STATS = 100;
const HEAL_THRESHOLD = 10;

function updateStats(data) {
    document.getElementById("godzilla_health").textContent = data.godzilla.health;
    document.getElementById("godzilla_strength").textContent = data.godzilla.strength || 40;
    document.getElementById("godzilla_intel").textContent = data.godzilla.intelligence;
    document.getElementById("godzilla_stamina").textContent = data.godzilla.stamina;

    document.getElementById("kong_health").textContent = data.kong.health;
    document.getElementById("kong_strength").textContent = data.kong.strength || 60;
    document.getElementById("kong_intel").textContent = data.kong.intelligence;
    document.getElementById("kong_stamina").textContent = data.kong.stamina;

    // Show last action
    actionElem.textContent = data.message || "";

    // Populate history
    historyElem.innerHTML = "";
    data.history.forEach(msg => {
        const li = document.createElement("li");
        li.textContent = msg;
        historyElem.appendChild(li);
    });

    // Disable heal buttons based on intelligence/health/turn
    godzillaHeal.disabled = data.godzilla.intelligence < HEAL_THRESHOLD || data.godzilla.health >= MAX_STATS || currentTurn !== "godzilla";
    kongHeal.disabled = data.kong.intelligence < HEAL_THRESHOLD || data.kong.health >= MAX_STATS || currentTurn !== "kong";

    newFight.disabled = (data.godzilla.health === MAX_STATS && data.godzilla.stamina === MAX_STATS &&
                         data.kong.health === MAX_STATS && data.kong.stamina === MAX_STATS);
}

function setTurn(turn) {
    currentTurn = turn;
    godzillaAttack.disabled = godzillaPower.disabled = godzillaHeal.disabled = turn !== "godzilla";
    kongAttack.disabled = kongSneak.disabled = kongHeal.disabled = turn !== "kong";

    if(turn === "godzilla") {
        godzillaAttack.disabled = parseInt(document.getElementById("godzilla_stamina").textContent) < 15;
        godzillaPower.disabled = parseInt(document.getElementById("godzilla_stamina").textContent) < 30;
        godzillaHeal.disabled = (parseInt(document.getElementById("godzilla_health").textContent) >= MAX_STATS ||
                                parseInt(document.getElementById("godzilla_intel").textContent) < HEAL_THRESHOLD);
    } else {
        kongAttack.disabled = parseInt(document.getElementById("kong_stamina").textContent) < 15;
        kongSneak.disabled = parseInt(document.getElementById("kong_stamina").textContent) < 40;
        kongHeal.disabled = (parseInt(document.getElementById("kong_health").textContent) >= MAX_STATS ||
                            parseInt(document.getElementById("kong_intel").textContent) < HEAL_THRESHOLD);
    }
}

function disableButtons() {
    godzillaAttack.disabled = godzillaPower.disabled = godzillaHeal.disabled = true;
    kongAttack.disabled = kongSneak.disabled = kongHeal.disabled = true;
}

function checkWinner(data) {
    if ((data.godzilla.health <= MIN_STATS && data.kong.health <= MIN_STATS) || 
        (data.godzilla.stamina < 15 && data.kong.stamina < 15 && data.godzilla.health > 0 && data.kong.health > 0)) {
        winnerElem.textContent = "It's a draw!";
        actionElem.textContent = "";
        disableButtons();
    } else if (data.godzilla.health <= 0 || (data.godzilla.intelligence < HEAL_THRESHOLD && data.godzilla.stamina < 15)) {
        winnerElem.textContent = "Kong wins!";
        actionElem.textContent = "";
        disableButtons();
    } else if (data.kong.health <= 0 || (data.kong.intelligence < HEAL_THRESHOLD && data.kong.stamina < 15)) {
        winnerElem.textContent = "Godzilla wins!";
        actionElem.textContent = "";
        disableButtons();
    } else {
        setTurn(currentTurn === "godzilla" ? "kong" : "godzilla");
        winnerElem.textContent = (currentTurn === "godzilla") ? "Godzilla's turn!" : "Kong's turn!";
        actionElem.textContent = "";
    }
}

function action(type, player = null) {
    const formData = new URLSearchParams();
    formData.append('action', type);
    if(player) formData.append('player', player);

    fetch("fightingAction.php", { method: "POST", body: formData })
        .then(res => res.json())
        .then(data => {
            updateStats(data);
            if(type !== 'reset') checkWinner(data);
            else {
                setTurn('godzilla');
                winnerElem.textContent = "Godzilla's turn!";
                actionElem.textContent = "";
            }
        }).catch(err => console.error(err));
}

// Event listeners
godzillaAttack.addEventListener("click", () => action('attack', 'godzilla'));
godzillaPower.addEventListener("click", () => action('powerstrike', 'godzilla'));
godzillaHeal.addEventListener("click", () => action('heal', 'godzilla'));

kongAttack.addEventListener("click", () => action('attack', 'kong'));
kongSneak.addEventListener("click", () => action('sneakattack', 'kong'));
kongHeal.addEventListener("click", () => action('heal', 'kong'));

newFight.addEventListener("click", () => action('reset'));

// Init
setTurn("godzilla");
winnerElem.textContent = "Godzilla's turn!";
actionElem.textContent = "";
