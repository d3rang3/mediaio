

$(document).ready(function () {

    loadPage();

});

async function loadPage() {

    const Container = document.getElementById('confirmEvents');
    Container.innerHTML = '';

    // Get the events from database

    const response = await $.ajax({
        url: "../../ItemManager.php",
        method: "POST",
        data: {
            mode: "getItemsForConfirmation"
        }
    });

    const events = JSON.parse(response);

    //console.log(events);

    if (events.length == 0) {
        const noItems = document.createElement("div");
        noItems.classList.add("alert", "alert-info", "mt-3", "text-center");
        noItems.style.width = "400px";
        noItems.innerHTML = "Semmi sem vár elfogadásra!";
        Container.appendChild(noItems);

        Container.style.display = "flex";
        Container.style.justifyContent = "center";
        Container.style.alignItems = "center";
        return;
    }


    // Get user information (name, username)

    const users = JSON.parse(await $.ajax({
        url: "../../Accounting.php",
        method: "POST",
        data: {
            mode: "getPublicUserInfo"
        }
    }));

    // Create the event cards
    events.forEach(event => {
        const user = users.find(user => user.idUsers == event.UserID);

        const card = document.createElement('div');
        card.classList.add('card');
        card.classList.add('mb-3');

        const cardBody = document.createElement('div');
        cardBody.classList.add('card-body', 'confirm-card');

        const cardNameDiv = document.createElement('div');
        cardNameDiv.classList.add('card-name-div');
        cardBody.appendChild(cardNameDiv);

        const cardTitle = document.createElement('h5');
        cardTitle.classList.add('card-title');
        cardTitle.textContent = `${user.lastName} ${user.firstName}`
        cardNameDiv.appendChild(cardTitle);

        const cardUsername = document.createElement('p');
        cardUsername.classList.add('card-username');
        cardUsername.textContent = user.usernameUsers;
        cardNameDiv.appendChild(cardUsername);

        // Second part of the card, the taken items list

        const takenItems = document.createElement('div');
        takenItems.classList.add('taken-items');
        cardBody.appendChild(takenItems);

        const items = JSON.parse(event.Items);

        for (let i = 0; i < 5; i++) {
            if (i >= items.length) {
                break;
            }
            const item = items[i];
            const itemDiv = document.createElement('li');
            itemDiv.classList.add('taken-item');
            itemDiv.textContent = `${item.name} - ${item.uid}`;
            takenItems.appendChild(itemDiv);
        }
        if (items.length > 5) {
            const itemDiv = document.createElement('div');
            itemDiv.style.fontStyle = 'italic';
            itemDiv.textContent = `Még ${items.length - 5} eszköz...`;
            takenItems.appendChild(itemDiv);
        }


        // Third part of the card, the confirm button and the timestamp

        const cardButtonHolder = document.createElement('div');
        cardButtonHolder.classList.add('card-button-holder');
        cardBody.appendChild(cardButtonHolder);

        const cardTimestamp = document.createElement('p');
        cardTimestamp.classList.add('card-timestamp');
        cardTimestamp.style.marginBottom = '10px';
        let date = new Date(event.Date);
        let formattedDate = `${date.getFullYear()}.${(date.getMonth() + 1).toString().padStart(2, '0')}.${date.getDate().toString().padStart(2, '0')} ${date.getHours().toString().padStart(2, '0')}:${date.getMinutes().toString().padStart(2, '0')}:${date.getSeconds().toString().padStart(2, '0')}`;
        cardTimestamp.textContent = formattedDate;
        cardButtonHolder.appendChild(cardTimestamp);

        const inOrOut = document.createElement('p');
        inOrOut.classList.add('card-timestamp');
        inOrOut.style.marginBottom = '10px';
        inOrOut.style.fontWeight = 'bold';
        inOrOut.textContent = event.Event == 'OUT' ? 'Kiadás' : 'Visszahozás';
        cardButtonHolder.appendChild(inOrOut);

        const confirmButton = document.createElement('button');
        confirmButton.classList.add('btn');
        confirmButton.classList.add(event.Event == 'OUT' ? 'btn-danger' : 'btn-success');
        confirmButton.textContent = 'Jóváhagyás';
        confirmButton.onclick = function () {
            // Open settings modal
            openSettingsModal(event);
        }
        cardButtonHolder.appendChild(confirmButton);

        card.appendChild(cardBody);
        Container.appendChild(card);
    });
}



async function openSettingsModal(event) {
    // Set the event data
    //document.getElementById('eventDate').textContent = event.Date;

    //Get event type and set modal title
    const eventType = event.Event;
    document.getElementById('SettingsModalLabel').textContent = eventType == 'OUT' ? 'Kivétel jóváhagyása' : 'Visszahozás jóváhagyása';

    // Set the event items
    const items = JSON.parse(event.Items);
    const itemList = document.getElementById('itemsList');
    itemList.innerHTML = '';
    items.forEach(item => {
        const itemDiv = document.createElement('div');
        itemDiv.classList.add('item', 'form-check', 'mb-2');

        const itemCheckbox = document.createElement('input');
        itemCheckbox.type = 'checkbox';
        itemCheckbox.classList.add('form-check-input');
        itemCheckbox.id = item.uid;
        itemCheckbox.checked = true;
        itemDiv.appendChild(itemCheckbox);

        const itemLabel = document.createElement('label');
        itemLabel.classList.add('form-check-label');
        itemLabel.style.marginLeft = '10px';
        itemLabel.htmlFor = item.uid;
        itemLabel.innerHTML = item.name;
        itemDiv.appendChild(itemLabel);
        itemList.appendChild(itemDiv);
    });

    // Add save button
    const submitButton = document.getElementById("submitButton");

    // Enable the button and remove the previous event listener
    submitButton.disabled = false;
    try {
        submitButton.removeEventListener('click', submitButtonHandler);
    } catch (error) {
        console.log("No event listener to remove");
    }
    // Create a new event handler with the current TaskId, taskType, and projectID
    submitButtonHandler = createSubmitButtonHandler(event.ID, event.Event);

    // Add the new event listener
    submitButton.addEventListener('click', submitButtonHandler);


    // Open the modal
    $('#SettingsModal').modal('show');

}

// Define the event handler function outside of the settings function
function createSubmitButtonHandler(eventID, eventDirection) {
    return async function submitButtonHandler(e) {
        e.preventDefault();
        this.disabled = true;
        setTimeout(() => {
            this.disabled = false;
        }, 1000);
        submitTransaction(eventID, eventDirection);
    };
}



async function submitTransaction(eventID, eventDirection) {
    console.log("Submitting transaction");

    const items = Array.from(document.querySelectorAll('.item input[type="checkbox"]')).map(item => {
        return {
            uid: item.id,
            declined: !item.checked
        };
    });

    console.log(items);

    const response = await $.ajax({
        url: "../../ItemManager.php",
        method: "POST",
        data: {
            mode: "confirmItems",
            eventID: eventID,
            items: JSON.stringify(items),
            direction: eventDirection
        }
    });

    console.log(response);

    if (response == 200) {
        // Close the modal
        $('#SettingsModal').modal('hide');
        // Reload the page
        loadPage();
    } else {
        alert("Hiba történt a jóváhagyás során.");
    }
}