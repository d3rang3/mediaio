
$(document).ready(function () {
    // Search

    const searchInput = document.getElementById("search");

    searchInput.addEventListener("input", function () {
        console.log("Searching items");
        const items = Array.from(document.getElementsByClassName("leltarItem"));
        const inputValue = searchInput.value.toLowerCase();
        const showAvailable = document.getElementById("show_unavailable").checked;
        const filterSettings = Array.from(document.getElementsByClassName("filterCheckbox")).filter(checkbox => checkbox.checked).map(checkbox => checkbox.getAttribute("data-filter"));


        items.forEach(item => {
            // Get the label element of the item
            const itemLabelElement = item.querySelector(".leltarItemLabel");

            // Construct the original label using the parent's data-name attribute and the item's id
            const originalItemLabel = `${itemLabelElement.parentElement.getAttribute("data-name")} - ${item.id}`;

            // Convert the original label to lowercase for comparison
            const itemName = originalItemLabel.toLowerCase();

            // Check if the item is available
            const isAvailable = item.getAttribute("data-available") == "true";

            // Check if the item meets the filter criteria
            const inFilterCriteria = filterSettings.length === 0 || filterSettings.includes(item.getAttribute("data-takerestrict"));

            // Determine if the item should be displayed based on the input value, availability, and filter criteria
            const shouldDisplay = itemName.includes(inputValue) && inFilterCriteria && (isAvailable || !showAvailable);

            // Set the display style of the item based on the shouldDisplay flag
            item.style.display = shouldDisplay ? "flex" : "none";

            // If the item should be displayed and there is an input value, highlight the matching text in the label
            if (shouldDisplay && inputValue) {
                const highlightedLabel = originalItemLabel.replace(new RegExp(`(${inputValue})`, 'gi'), '<span class="highlight">$1</span>');
                itemLabelElement.innerHTML = itemLabelElement.innerHTML !== highlightedLabel ? highlightedLabel : itemLabelElement.innerHTML;
            } else {
                // If the item should not be displayed or there is no input value, remove any existing highlights from the label
                itemLabelElement.innerHTML = itemLabelElement.innerHTML !== originalItemLabel ? originalItemLabel : itemLabelElement.innerHTML;
            }
        });
    });

    // Add event listener to only show unavailable items checkbox
    const showUnavailableCheckbox = document.getElementById("show_unavailable");

    showUnavailableCheckbox.addEventListener("change", function () {
        searchInput.dispatchEvent(new Event("input"));
    });


    // Add event listeners to filter checkboxes and their divs
    const holderDivs = document.querySelectorAll(".dropdown-item");

    holderDivs.forEach(holderDiv => {
        holderDiv.addEventListener("click", function () {
            const checkbox = holderDiv.querySelector(".filterCheckbox");
            checkbox.checked = !checkbox.checked;
            searchInput.dispatchEvent(new Event("input"));
        });
    });
});



async function loadItems() {
    const itemsList = document.getElementById("itemsList");
    itemsList.innerHTML = "";

    //Get items from server
    const response = JSON.parse(await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "getItems"
        }
    }));

    //Get userinfo

    const users = JSON.parse(await $.ajax({
        url: "../Accounting.php",
        method: "POST",
        data: {
            mode: "getPublicUserInfo"
        }
    }));

    //console.log(users);

    response.forEach(item => {
        if (item.TakeRestrict == 'ü') {
            return;
        }
        const itemElement = document.createElement("div");
        itemElement.classList.add("form-check", "mb-1", "leltarItem");
        itemElement.setAttribute("data-takeRestrict", item.TakeRestrict);
        itemElement.setAttribute("data-status", item.Status);
        itemElement.setAttribute("data-main-id", item.ID);
        itemElement.setAttribute("data-name", item.Nev);
        itemElement.id = `${item.UID}`;

        const checkBox = document.createElement("input");
        checkBox.type = "checkbox";
        switch (item.TakeRestrict) {
            case '*':
                checkBox.disabled = roleLevel < 5 || item.Status == 0 || item.Status == 2;
                itemElement.setAttribute("data-available", roleLevel >= 5 ? "true" : "false");
                if (item.Status == 1 && roleLevel >= 5) {
                    itemElement.onclick = () => {
                        toggleSelectItem(item);
                    };
                }
                itemElement.classList.add("special");
                break;
            case 'e':
                checkBox.disabled = roleLevel < 3 || item.Status == 0 || item.Status == 2;
                itemElement.setAttribute("data-available", roleLevel >= 3 ? "true" : "false");
                if (item.Status == 1 && roleLevel >= 3) {
                    itemElement.onclick = () => {
                        toggleSelectItem(item);
                    };
                }
                itemElement.classList.add("event");
                break;
            case 's':
                checkBox.disabled = roleLevel < 2 || item.Status == 0 || item.Status == 2;
                itemElement.setAttribute("data-available", roleLevel >= 2 ? "true" : "false");
                if (item.Status == 1 && roleLevel >= 2) {
                    itemElement.onclick = () => {
                        toggleSelectItem(item);
                    };
                }
                itemElement.classList.add("studio");
                break;
            default:
                checkBox.disabled = item.Status == 0 || item.Status == 2;
                itemElement.setAttribute("data-available", "true");
                if (item.Status == 1) {
                    itemElement.onclick = () => {
                        toggleSelectItem(item);
                    };
                }
                break;
        }
        if (item.Status == 0 || item.Status == 2) {
            itemElement.setAttribute("data-available", "false");
        }
        checkBox.classList.add("form-check-input", "leltarItemCheckbox");

        itemElement.appendChild(checkBox);


        const itemLabel = document.createElement("label");
        itemLabel.classList.add("form-check-label", "leltarItemLabel");
        if (item.Status == 1) {
            itemLabel.innerHTML = `${item.Nev} - ${item.UID}`;
        } else {
            const RentByUsername = users.find((user) => user.idUsers == item.RentBy)?.usernameUsers || '';
            itemLabel.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="Kivette: ${RentByUsername}">${item.Nev} - ${item.UID}</a>`;
        }
        itemElement.appendChild(itemLabel);

        itemsList.appendChild(itemElement);
    });

    reloadSavedSelections();
}


function toggleSelectItem(item) {
    const itemElement = document.getElementById(item.UID);
    const checkBox = itemElement.querySelector(".leltarItemCheckbox");

    if (itemElement.classList.contains("selected")) {
        checkBox.checked = false;
        let selectedCards = document.querySelectorAll(`#selected-${item.UID}`);
        selectedCards.forEach(card => {
            card.remove();
        });
        parseInt(badge.textContent = parseInt(badge.textContent) - 1);
    } else {
        checkBox.checked = true;
        addItemCard(item);
        parseInt(badge.textContent = parseInt(badge.textContent) + 1);
    }

    itemElement.classList.toggle("selected");
    updateSelectionCookie();
}



function addItemCard(item) {
    const selectedItems = document.getElementsByClassName("selectedList");

    const card = document.createElement("div");
    card.classList.add("card", "mb-2", "selected-card");
    card.id = `selected-${item.UID}`;

    const cardBody = document.createElement("div");
    cardBody.classList.add("card-body", "d-flex", "justify-content-between");

    const infoDiv = document.createElement("div");

    const cardTitle = document.createElement("h5");
    cardTitle.classList.add("card-title");
    cardTitle.innerHTML = item.Nev;

    const cardText = document.createElement("p");
    cardText.classList.add("card-text");
    cardText.innerHTML = item.UID;

    infoDiv.appendChild(cardTitle);
    infoDiv.appendChild(cardText);

    function attachRemoveButtonListener(button, item) {
        button.onclick = () => {
            toggleSelectItem(item);
        }
    }

    const removeButton = document.createElement("button");
    removeButton.classList.add("btn", "btn-danger");
    removeButton.innerHTML = `<i class="fas fa-trash-alt"></i>`;
    attachRemoveButtonListener(removeButton, item);

    cardBody.appendChild(infoDiv);
    cardBody.appendChild(removeButton);
    card.appendChild(cardBody);

    Array.from(selectedItems).forEach(selectedList => {
        const clonedCard = card.cloneNode(true);
        const clonedRemoveButton = clonedCard.querySelector(".btn-danger");
        attachRemoveButtonListener(clonedRemoveButton, item);
        selectedList.appendChild(clonedCard);
    });
}


function deselect_all() {
    console.log("Deselecting all items");

    document.querySelectorAll('.selected').forEach(item => {
        item.classList.remove("selected");
        item.querySelector(".leltarItemCheckbox").checked = false;
    });

    document.querySelectorAll('.selected-card').forEach(item => {
        item.remove();
    });

    //decideGiveToAnotherPerson_visibility();
    //parseInt(badge.textContent = 0);
    updateSelectionCookie();
}




async function showPresetsModal() {
    $('#presets_Modal').modal('show');

    //get Preset Items
    const response = await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "getPresets"
        }
    });

    //Convert rerponse to JSON
    var presets = JSON.parse(response);
    //For each user add a select option to givetoAnotherPerson_UserName
    if (presets.length > 0) {
        $('#presetsLoading').hide();
    }
    $('#presetsContainer').html('');

    presets.forEach((preset, i) => {
        const button = document.createElement('button');
        button.className = 'btn mediaBlue position-relative';
        button.id = `presetButton${i}`;
        button.onclick = function () { addItems(preset.Items); };
        button.innerHTML = `${preset.Name}<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"></span>`;

        document.getElementById('presetsContainer').appendChild(button);
    });

    document.getElementById('notAvailableItems').innerHTML = '';
}


function addItems(items) {
    console.log("Adding preset items");

    items = JSON.parse(items);
    items = items.items;

    let notAvailableItems = [];

    items.forEach(item => {
        const itemElement = document.getElementById(item);

        // If item is not found, skip it
        if (!itemElement) {
            return;
        }

        // Get if item is available
        const isAvailable = itemElement.getAttribute("data-available") == "true";

        // If item is not available, add it to the notAvailableItems array
        if (!isAvailable) {
            notAvailableItems.push(item);
        } else {
            // If item is available, select it
            itemElement.click();
        }
    });

    console.log(`Not available items: ${notAvailableItems}`);

    if (notAvailableItems.length > 0) {
        const notAvailableItemsHolder = document.getElementById("notAvailableItems");
        notAvailableItemsHolder.innerHTML = "";

        // Create not available label:
        const notAvailableLabel = document.createElement("h5");
        notAvailableLabel.innerHTML = "Az alábbi elemek nem elérhetőek:";
        notAvailableItemsHolder.appendChild(notAvailableLabel);

        notAvailableItems.forEach(item => {
            const itemElement = document.getElementById(item);

            // Create a list with the not available items
            const listItem = document.createElement("li");
            listItem.classList.add("list-group-item");
            listItem.innerHTML = itemElement.querySelector(".leltarItemLabel").textContent;
            notAvailableItemsHolder.appendChild(listItem);

        });
    }
}

