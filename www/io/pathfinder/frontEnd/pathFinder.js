

$(document).ready(function () {

    loadItemsList();


    const searchInput = document.getElementById("search");

    searchInput.addEventListener("input", function () {
        const items = Array.from(document.getElementsByClassName("searchItem"));
        const inputValue = searchInput.value.toLowerCase();

        let firstVisibleItem = null;

        items.forEach(item => {
            // Restore the original item label
            const originalItemLabel = `${item.getAttribute("data-name")} - ${item.id}`;
            const itemName = originalItemLabel.toLowerCase();

            const shouldDisplay = inputValue && itemName.includes(inputValue);

            item.style.display = shouldDisplay ? "block" : "none";

            // Remove 'selected' class from all items
            item.classList.remove("selected");

            // Add 'selected' class to the first item that should be displayed
            if (shouldDisplay && firstVisibleItem === null) {
                item.classList.add("selected");
                firstVisibleItem = true;
            }

            if (inputValue) {
                // Highlight matching characters
                const regex = new RegExp(`(${inputValue})`, 'gi');
                const highlightedLabel = originalItemLabel.replace(regex, '<span class="highlight">$1</span>');

                if (item.innerHTML !== highlightedLabel) {
                    item.innerHTML = highlightedLabel;
                }
            } else if (item.innerHTML !== originalItemLabel) {
                item.innerHTML = originalItemLabel;
            }
        });

        firstVisibleItem = null;
    });

    // Add eventlistener to keyboard events

    searchInput.addEventListener("keydown", function (event) {
        if (event.key === "ArrowDown") {
            const items = Array.from(document.getElementsByClassName("searchItem"));
            const visibleItems = items.filter(item => item.style.display !== "none");

            const selectedItem = visibleItems.find(item => item.classList.contains("selected"));

            if (selectedItem) {
                const selectedIndex = visibleItems.indexOf(selectedItem);
                if (selectedIndex + 1 < visibleItems.length) {
                    selectedItem.classList.remove("selected");
                    visibleItems[selectedIndex + 1].classList.add("selected");
                    visibleItems[selectedIndex + 1].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } else {
                visibleItems[0].classList.add("selected");
                visibleItems[0].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        } else if (event.key === "ArrowUp") {
            const items = Array.from(document.getElementsByClassName("searchItem"));
            const visibleItems = items.filter(item => item.style.display !== "none");

            const selectedItem = visibleItems.find(item => item.classList.contains("selected"));

            if (selectedItem) {
                const selectedIndex = visibleItems.indexOf(selectedItem);
                if (selectedIndex - 1 >= 0) {
                    selectedItem.classList.remove("selected");
                    visibleItems[selectedIndex - 1].classList.add("selected");
                    visibleItems[selectedIndex - 1].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }
            } else {
                visibleItems[visibleItems.length - 1].classList.add("selected");
                visibleItems[visibleItems.length - 1].scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        } else if (event.key === "Enter") {
            const selectedItem = document.querySelector(".searchItem.selected");
            if (selectedItem) {
                searchItem({
                    UID: selectedItem.id,
                    Nev: selectedItem.getAttribute("data-name")
                });
            }
        }
    });
});


async function loadItemsList() {
    const response = await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "getItemNames"
        },
    });

    const items = JSON.parse(response);

    const itemListHolder = document.getElementById("itemsList");

    items.forEach(item => {
        if (item.TakeRestrict == 'ü') {
            return;
        }
        createListItem(item, itemListHolder);
    });

}


function createListItem(item, itemListHolder) {

    const itemElement = document.createElement("button");
    itemElement.classList.add("searchItem", "list-group-item", "list-group-item-action");
    itemElement.setAttribute("data-status", item.Status);
    itemElement.setAttribute("data-main-id", item.ID);
    itemElement.setAttribute("data-name", item.Nev);
    itemElement.id = `${item.UID}`;
    itemElement.textContent = item.Nev;
    itemElement.onclick = () => {
        searchItem(item);
    };

    itemListHolder.appendChild(itemElement);
}



async function searchItem(item) {

    const response = await $.ajax({
        url: "../ItemManager.php",
        method: "POST",
        data: {
            mode: "getItemHistory",
            itemUID: item.UID
        },
    });

    const users = JSON.parse(await $.ajax({
        url: "../../Accounting.php",
        type: "POST",
        data: {
            mode: "getPublicUserInfo",
        },
    }));

    document.getElementById("search").value = "";
    document.getElementById("search").dispatchEvent(new Event("input"));

    const History = JSON.parse(response);
    console.log(History);

    document.getElementById('itemTitle').innerHTML = `${item.Nev} - ${item.UID}`;

    const itemHistoryTable = document.getElementById("itemHistoryTable");
    itemHistoryTable.innerHTML = "";

    // Create table header
    const header = document.createElement("thead");
    const headerRow = document.createElement("tr");

    const dateHeader = document.createElement("th");
    dateHeader.scope = "col";
    dateHeader.textContent = "Dátum";

    const userHeader = document.createElement("th");
    userHeader.scope = "col";
    userHeader.textContent = "Felhasználó";

    const actionHeader = document.createElement("th");
    actionHeader.scope = "col";
    actionHeader.textContent = "Esemény";

    const acknoledgedHeader = document.createElement("th");
    acknoledgedHeader.scope = "col";
    acknoledgedHeader.textContent = "Ellenőrizte";

    headerRow.appendChild(dateHeader);
    headerRow.appendChild(userHeader);
    headerRow.appendChild(actionHeader);
    headerRow.appendChild(acknoledgedHeader);
    header.appendChild(headerRow);

    itemHistoryTable.appendChild(header);

    // Create table body
    const body = document.createElement("tbody");
    itemHistoryTable.appendChild(body);

    History.forEach(historyItem => {
        const row = document.createElement("tr");
        switch (historyItem.Event) {
            case 'IN':
                row.classList.add("table-success");
                break;
            case 'OUT':
                row.classList.add("table-danger");
                break;
            case 'SERVICE':
                row.classList.add("table-warning");
                break;
        }

        const date = document.createElement("td");
        date.textContent = historyItem.Date;

        let userName = users.find(user => user.idUsers == historyItem.UserID).usernameUsers;

        const user = document.createElement("td");
        user.textContent = userName;

        const action = document.createElement("td");
        action.textContent = historyItem.Event;

        const acknoledged = document.createElement("td");
        acknoledged.textContent = historyItem.ACKBY;

        row.appendChild(date);
        row.appendChild(user);
        row.appendChild(action);
        row.appendChild(acknoledged);

        body.appendChild(row);
    });

}