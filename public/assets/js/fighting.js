const godzillaFight = document.getElementById("godzilla_fight");
const kongFight = document.getElementById("kong_fight");
const godzillaHeal = document.getElementById("godzilla_heal");
const kongHeal = document.getElementById("kong_heal");
const newFight = document.getElementById("new_fight");
const winnerElem = document.getElementById("winner_message");


function updateStats(data) {
    document.getElementById("godzilla_stamina").textContent = data.godzilla.stamina;
    document.getElementById("godzilla_intel").textContent = data.godzilla.intelligence;
    document.getElementById("godzilla_life").textContent = data.godzilla.life;

    document.getElementById("kong_stamina").textContent = data.kong.stamina;
    document.getElementById("kong_intel").textContent = data.kong.intelligence;
    document.getElementById("kong_life").textContent = data.kong.life;
}

// Disable buttons when game ends
function disableButtons() {
    godzillaFight.disabled = true;
    kongFight.disabled = true;
    godzillaHeal.disabled = true;
    kongHeal.disabled = true;
}


function enableButtons() {
    godzillaFight.disabled = false;
    kongFight.disabled = false;
    godzillaHeal.disabled = false;
    kongHeal.disabled = false;
}


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
    } else if (data.godzilla.stamina < 15) {
        winnerElem.textContent = "Godzilla can't attack anymore!";
    } else if (data.kong.stamina < 15) {
        winnerElem.textContent = "King Kong can't attack anymore!";
    } else {
        winnerElem.textContent = "";
    }
}


function attack(attacker) {
    fetch("fight.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "attacker=" + attacker
    })
    .then(response => response.json())
    .then(data => {
        updateStats(data);
        checkWinner(data);
    })
    .catch(err => console.error("Error:", err));
}


function heal(healer) {
    fetch("heal.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "healer=" + healer
    })
    .then(response => response.json())
    .then(data => {
        updateStats(data);
        checkWinner(data); // optional to prevent healing after game over
    })
    .catch(err => console.error("Error:", err));
}


godzillaFight.addEventListener("click", () => attack('godzilla'));
kongFight.addEventListener("click", () => attack('kong'));
godzillaHeal.addEventListener("click", () => heal('godzilla'));
kongHeal.addEventListener("click", () => heal('kong'));


newFight.addEventListener("click", () => {
    fetch("newFight.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" }
    })
    .then(response => response.json())
    .then(data => {
        updateStats(data);
        winnerElem.textContent = "";
        enableButtons();
    })
    .catch(err => console.error("Error:", err));
});
