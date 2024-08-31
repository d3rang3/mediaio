let formAnswers = [];

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

    // Find the form answer
    let currentAnswer = formAnswers.find(answer => answer.ID == id);
    currentAnswer = JSON.parse(currentAnswer.UserAnswers);

    console.log(currentAnswer);

    const formContainer = document.getElementById("form-body");
    formContainer.innerHTML = "";

    formElements = [];

    currentAnswer?.forEach((element) => {
        let { type: type, id: id, question: question, details: details, required: required, options: options, answer: answer } = element;

        // Create a new form element
        let formElement = new FormElement(id, type, question, details, required, options, answer);
        formElement.createElement(formContainer, "answer");
        formElements.push(formElement);
    });

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

    let currentForm = JSON.parse(currentFormState.Data);

    console.log(currentForm);

    for (var i = 0; i < currentForm.length; i++) {
        var th = document.createElement("th"); // Fejléc cellák

        //Getting question name
        var question = currentForm[i].question;
        if (question == "") {
            switch (currentForm[i].type) {
                case "email":
                    question = "<i>Email cím</i>";
                    break;
                case "shortText":
                    question = "<i>Szöveg</i>";
                    break;
                case "longtext":
                    question = "<i>Hosszú szöveg</i>";
                    break;
                case "dropdown":
                    question = "<i>Legördülő menü</i>";
                    break;
                case "radio":
                    question = "<i>Feleletválasztós</i>";
                    break;
                case "checkbox":
                    question = "<i>Jelölőnégyzet</i>";
                    break;
                case "scaleGrid":
                    question = "<i>Feleletválasztós rács</i>";
                    break;
                case "date":
                    question = "<i>Dátum</i>";
                    break;
                case "time":
                    question = "<i>Idő</i>";
                    break;
                case "file":
                    question = "<i>Fájl</i>";
                    break;
                default:
                    question = "Nem található kérdés";
            }
        }

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

    function getElementAnswer(element, answer) {
        const elementType = element.type;
        const elementId = element.id;
        let elementAnswer;

        const answerData = answer.find(data => data.id == elementId);
        if (answerData?.type !== elementType) {
            console.error(`Element type mismatch: ${answerData?.type} !== ${elementType}`);
            return '<i>Nem megválaszolt</i>';
        }

        if (answerData) {
            switch (elementType) {
                case 'checkbox':
                case 'radio':
                    elementAnswer = getCheckedAnswer(answerData.answer);
                    break;
                case 'scaleGrid':
                    //elementAnswer = getScaleGridAnswer(answerData.value);
                    elementAnswer = '<i>In development</i>';
                    break;
                default:
                    elementAnswer = answerData.answer !== '' ? answerData.answer : '<i>Nem megválaszolt</i>';
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
        // Return comma separated values in string
        return value.join(", ");
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