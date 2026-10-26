// Display a message when a blood group is selected.

document.querySelectorAll(".blood-card").forEach(function (card) {
    card.addEventListener("click", function () {
        console.log("Selected blood group: " +
            this.querySelector("strong").textContent);
    });
});