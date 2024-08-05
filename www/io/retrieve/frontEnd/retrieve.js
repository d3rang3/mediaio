function AnnounceDamage() {
    window.location.href = "../utility/damage_report/announce_Damage.php";
}


$(document).ready(function () {

    loadUserItems();

    // Add event listener to manual retrieve checkbox

    const manualRetrieve = document.getElementById("manual_Retrieve");

    manualRetrieve.addEventListener("change", function () {
        const items = document.getElementsByClassName("card");
        if (this.checked) {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.add("selectable");
            }
        } else {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.remove("selectable");
            }
        }
    });


});



async function loadUserItems() {

    const response = JSON.parse(await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "listUserItems"
        }
    }));

    //console.log(response);

    const itemHolder = document.getElementById("itemsHolder");
    itemHolder.innerHTML = "";

    if (response.length == 0) {
        const noItems = document.createElement("div");
        noItems.classList.add("alert", "alert-info", "mt-3", "text-center");
        noItems.style.width = "400px";
        noItems.innerHTML = "Nincsen nálad egy tárgy sem!";
        itemHolder.appendChild(noItems);

        itemHolder.style.gridTemplateColumns = "none";
        itemHolder.style.justifyContent = "center";
        itemHolder.style.alignItems = "center";

        document.getElementById("submission").style.display = "none";
        const ownerChangeElement = document.getElementById("ownerChange");
        if (ownerChangeElement) {
            ownerChangeElement.style.display = "none";
        }
        return;
    }

    response.forEach(element => {
        createItemCard(element);
    });

    reloadSavedSelections();

}


function createItemCard(item) {
    const isPlanned = item.isPlanned && item.Status == 1;
    const itemHolder = document.getElementById("itemsHolder");

    const itemCard = document.createElement("div");
    itemCard.classList.add("card", "itemCard");
    itemCard.id = item.UID;
    itemCard.setAttribute("data-name", item.Nev);
    isPlanned ? itemCard.classList.add("planned") : null;
    itemCard.onclick = function () {
        if (this.classList.contains("selectable")) {
            toggleSelectItem(item);
        } else {
            warningToast("Engedélyezd a tárgyak kiválasztását!");
            // Shake the manual retrieve button
            document.getElementById("manualHolder").classList.add("animate_shakeY");
            setTimeout(() => {
                document.getElementById("manualHolder").classList.remove("animate_shakeY");
            }, 1000);
        }
    }


    const cardBody = document.createElement("div");
    cardBody.classList.add("card-body");

    const textHolder = document.createElement("div");

    const cardTitle = document.createElement("h5");
    cardTitle.classList.add("card-title");
    cardTitle.innerHTML = item.Nev;
    textHolder.appendChild(cardTitle);

    const cardText = document.createElement("p");
    cardText.classList.add("card-text");
    cardText.innerHTML = item.UID;
    textHolder.appendChild(cardText);

    cardBody.appendChild(textHolder);

    if (isPlanned) {
        cardBody.classList.add("d-flex", "justify-content-between");
        const loadingSpinner = document.createElement("div");
        loadingSpinner.classList.add("spinner-border", "text-secondary", "spinner-border-sm");
        cardBody.appendChild(loadingSpinner);
    }




    itemCard.appendChild(cardBody);

    itemHolder.appendChild(itemCard);

}


function toggleSelectItem(item) {
    if (item.isPlanned && item.Status == 1) {
        warningToast("Ez a tárgy még nincs nálad, csak időzítve van!");
        return;
    }
    const itemElement = document.getElementById(item.UID);

    itemElement.classList.toggle("text-bg-success");
    itemElement.classList.toggle("selected");
    updateSelectionCookie();
}

async function loadUsersModal() {
    const users = JSON.parse(await $.ajax({
        url: "../Accounting.php",
        method: "POST",
        data: {
            mode: "getPublicUserInfo"
        }
    }));

    const select = document.getElementById("newOwnerSelect");
    select.innerHTML = "";

    const defaultOption = document.createElement("option");
    defaultOption.value = "";
    defaultOption.innerHTML = "Válassz egy felhasználót...";
    select.appendChild(defaultOption);

    users.forEach(user => {
        const option = document.createElement("option");
        option.value = user.idUsers;
        option.innerHTML = `${user.lastName} ${user.firstName}`;
        select.appendChild(option);
    });
}

async function changeOwner() {
    const selectedItems = document.getElementsByClassName("selected");
    if (selectedItems.length == 0) {
        warningToast("Nincs kiválasztva egy tárgy sem!");
        return;
    }

    const newOwner = document.getElementById("newOwnerSelect").value;
    if (newOwner == "") {
        warningToast("Nincs kiválasztva új tulajdonos!");
        return;
    }

    const itemsToChange = Array.from(selectedItems).map(item => ({
        uid: item.id,
        name: item.getAttribute("data-name"),
    }));

    const response = await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "changeOwner",
            items: JSON.stringify(itemsToChange),
            newOwner: newOwner
        }
    });

    if (response == 200) {
        successToast("Sikeres tulajdonosváltás!");
        loadUserItems();
        clearSelectionCookie();
        // Close modal
        $('#changeOwnerModal').modal('hide');
    } else {
        console.log(response);
        serverErrorToast();
    }
}

async function submitRetrieve() {
    if (!document.getElementById("intactItems").checked) {
        $('#confirmModal').modal('hide');
        warningToast("Ha sérülést észleltél, kérlek jelezd a vezetőség felé!");
        const damageButton = document.getElementById("AnnounceDamage");
        damageButton.classList.add("animate_shakeY");
        setTimeout(() => {
            damageButton.classList.remove("animate_shakeY");
        }, 1000);
        return;
    }

    const selectedItems = document.getElementsByClassName("selected");
    if (selectedItems.length == 0) {
        warningToast("Nincs kiválasztva egy tárgy sem!");
        return;
    }

    const itemsToRetrieve = Array.from(selectedItems).map(item => ({
        uid: item.id,
        name: item.getAttribute("data-name"),
    }));

    const response = await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "retrieveStaging",
            data: JSON.stringify(itemsToRetrieve),
        }
    });

    if (response == 200 || response == 201) {
        response == 200 ? successToast("Sikeres a tárgyak visszavétele!") : warningToast("Sikeres visszavétel, jóváhagyásra vár!");
        $('#confirmModal').modal('hide');
        document.getElementById("intactItems").checked = false;
        document.getElementById("manual_Retrieve").checked = false;
        loadUserItems();
        clearSelectionCookie();
    } else {
        console.log(response);
        serverErrorToast();
        clearSelectionCookie();
    }
}