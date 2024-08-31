// Save userInput to cookie

function saveUserInputToCookie() {
    console.log("Saving user input to cookie");

    const currentState = submitFormElements(formElements); //Get all form elements and answers
    //console.log(currentState);

    //Set cookie expire date to 1 day
    const d = new Date();
    d.setTime(d.getTime() + (1 * 24 * 60 * 60 * 1000));
    const expires = `expires=${d.toUTCString()}`;

    document.cookie = `userInput=${JSON.stringify(currentState)};${expires};path=/; `;
}

function reloadUserInput() {
    let userInput = getUserInputFromCookie();
    if (userInput !== "") {
        userInput = JSON.parse(userInput);
        console.log("Reloading user input from cookie");

        formElements.forEach((element) => {
            // Find the corresponding user input by id
            const input = userInput.find(input => input.id === element.id);
            if (input) {
                element.setAnswer(input.answer); // Assuming 'answer' is the property holding the answer
                element.loadUserinput();
            } else {
                console.warn(`No user input found for element with id ${element.id}`);
            }
        });
    }

}

//Get userInput from cookie
function getUserInputFromCookie() {
    var name = "userInput=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            var userInput = c.substring(name.length, c.length);
            return userInput;
        }
    }
    return null;
}

//Clear userInput cookie

function clearUserCookie() {
    document.cookie = "userInput=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
}


//Get form JSON

async function getFormJson(formId, formHash) {
    var formJson;
    if (formId != -1) {
        await $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "getForm", id: formId },
            success: function (data) {
                formJson = JSON.parse(data);
                formData = JSON.parse(formJson.Data);
            }
        });
    } else {
        await $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: { mode: "getForm", formHash: formHash },
            success: function (data) {
                formJson = JSON.parse(data);
                formData = JSON.parse(formJson.Data);
            }
        });
    }
    return formData;
}