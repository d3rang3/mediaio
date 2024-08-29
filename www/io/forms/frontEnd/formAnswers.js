

function showFormAnswers(id) {

    //Set button class
    setButtonClass("singleAnswer");

    //Set doboz max-width
    var doboz = document.getElementById("doboz");
    doboz.style.maxWidth = "800px";

    //Set table invisible
    var table = document.getElementById("answersTable");
    table.style.display = "none";

    console.log("Showing form answers: " + id);

    var UserInput;
    var formElements;

    for (var i = 0; i < formAnswers.length; i++) {
        if (formAnswers[i].ID == id) {
            UserInput = JSON.parse(formAnswers[i].UserAnswers);
            formElements = JSON.parse(formAnswers[i].FormState);
        }
    }


    var formContainer = document.getElementById("form-body");
    formContainer.innerHTML = "";
    //Load form elements
    for (var pos = 0; pos < formElements.length; pos++) {
        var element = formElements[pos];


        var elementType = element.type;
        var elementId = element.id;
        var elementPlace = element.place;
        var elementSettings = element.settings;
        var elementAnswer;

        for (var i = 0; i < UserInput.length; i++) {
            if (UserInput[i].id == (elementType + "-" + elementId)) {
                elementAnswer = UserInput[i].value;
            }
        }

        formContainer.appendChild(generateElement(elementType, elementId, elementPlace, elementSettings, "answer", elementAnswer));

    }

    //Set form visible
    formContainer.style.display = "block";
}


function showTable() {

    setButtonClass("table");

    //Set doboz max-width
    const doboz = document.getElementById("doboz");
    doboz.style.maxWidth = "1200px";

    //Empty table
    const headerHolder = document.getElementById("headerHolder");
    headerHolder.innerHTML = "";

    const answerHolder = document.getElementById("answerHolder");
    answerHolder.innerHTML = "";

    // Set form invisible
    const formContainer = document.getElementById("form-body");
    formContainer.style.display = "none";

    // Generate table header
    var idTh = document.createElement("th");
    idTh.innerHTML = "ID";
    idTh.scope = "col";
    headerHolder.appendChild(idTh);

    console.log(currentForm);

    for (var i = 0; i < currentForm.length; i++) {
        var th = document.createElement("th");

        //Getting question name
        var question = JSON.parse(currentForm[i].settings).question;

        th.innerHTML = question;
        th.scope = "col";
        headerHolder.appendChild(th);
    }

    // Generate table body

    function createRow(formAnswers, formElements) {
        const tr = document.createElement("tr");

        var idTd = document.createElement("td");
        idTd.innerHTML = formAnswers.ID;
        tr.appendChild(idTd);

        const formAnswersData = JSON.parse(formAnswers.UserAnswers);

        // Create cells
        for (var j = 0; j < formElements.length; j++) {
            var td = document.createElement("td");
            var elementAnswer = getElementAnswer(formElements[j], formAnswersData);
            td.innerHTML = elementAnswer;
            tr.appendChild(td);
        }

        const deleteAnswerButton = document.createElement("button");
        deleteAnswerButton.innerHTML = `<i class="fas fa-trash"></i>`;
        deleteAnswerButton.className = "btn btn-danger";
        deleteAnswerButton.onclick = function () {
            deleteAnswer(formAnswers.ID);
        }
        tr.appendChild(deleteAnswerButton);
        return tr;
    }

    function getElementAnswer(element, AnswerData) {
        const elementType = element.type;
        const elementId = element.id;
        let elementAnswer;

        const answerData = AnswerData.find(data => data.id === `${elementType}-${elementId}`);

        if (answerData) {
            switch (elementType) {
                case 'checkbox':
                case 'radio':
                    console.log(answerData);
                    elementAnswer = getCheckedAnswer(answerData.value) || '<i>In development</i>';
                    break;
                case 'scaleGrid':
                    elementAnswer = getScaleGridAnswer(answerData.value);
                    break;
                default:
                    elementAnswer = answerData.value !== '' ? answerData.value : '<i>Nem megválaszolt</i>';
            }
        }

        return elementAnswer || '<i>Nem megválaszolt</i>';
    }

    function getScaleGridAnswer(submission) {
        let answer = "";

        function getGrade(sub) {
            const gradeIndex = sub.answers.findIndex(answer => answer === 1);
            return gradeIndex >= 0 ? gradeIndex + 1 : 0;
        }

        for (let i = 0; i < submission.length; i++) {
            answer += `${submission[i].label}: ${getGrade(submission[i])}<br>`;
        }
        return answer;
    }

    function getCheckedAnswer(value) {
        const checkedAnswers = value
            .filter(answer => Boolean(Number(answer.split(":")[1])))
            .map(answer => answer.split(":")[0]);

        return checkedAnswers.join(", ");
    }

    for (var i = 0; i < formAnswers.length; i++) {
        var tr = createRow(formAnswers[i], currentForm);
        answerHolder.appendChild(tr);
    }

    // Set table visible
    var table = document.getElementById("answersTable");
    table.style.display = "table";

}


async function deleteAnswer(id) {
    console.log("Deleting answer: " + id);

    const response = await $.ajax({
        url: "../formManager.php",
        type: "POST",
        data: {
            mode: "deleteAnswer",
            id: id
        }
    });

    if (response == 200) {
        alert("Válasz törölve");
        showTable();
    } else {
        alert("Hiba történt a válasz törlése közben");
    }
}