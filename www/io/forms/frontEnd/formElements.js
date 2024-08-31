// List which contains the form elements
let formElements = [];

class FormElement {
    id;
    type = '';
    question = '';
    details = {};
    required = false;
    options = [];
    answer = {};

    // Methods:
    constructor(id, type, question, details, required, options, answer = {}) {
        this.id = id;
        this.type = type;
        this.question = question;
        this.details = details;
        this.required = required;
        this.options = options;
        this.answer = answer;
    }

    createElement(container, state) {

        // Main div containing the form element
        const mainDIV = document.createElement('div');
        mainDIV.id = `${this.type}-${this.id}`;
        mainDIV.classList.add("mb-3", "form-member", "draggable");
        state == "editor" ? mainDIV.classList.add("editor") : mainDIV.classList.add("user");
        mainDIV.setAttribute('data-type', this.type);
        mainDIV.setAttribute('data-required', this.required);

        // Create a div for the ui elements
        const uiDiv = document.createElement('div');
        uiDiv.classList.add("form-control", "form-options");
        mainDIV.appendChild(uiDiv);

        // Question input or label
        if (state == "editor") {
            const questionInput = document.createElement('input');
            questionInput.type = 'text';
            questionInput.classList.add("form-control", "editorLabel");
            questionInput.placeholder = 'Kérdés';
            questionInput.value = this.question ? this.question : '';
            uiDiv.appendChild(questionInput);
        } else {
            const questionLabel = document.createElement('label');
            questionLabel.classList.add("form-label");
            if (this.required) {
                questionLabel.innerHTML = (this.question ? this.question : '') + "<span style='color: red;'> *</span>";
            } else {
                questionLabel.innerHTML = this.question ? this.question : '';
            }
            uiDiv.appendChild(questionLabel);
        }


        // Based on the type of the form element, we generate the specific editor
        switch (this.type) {
            case "email":
                uiDiv.appendChild(this.generateEmail(state));
                break;
            case "shortText":
                uiDiv.appendChild(this.generateShortText(state));
                break;
            case "longText":
                uiDiv.appendChild(this.generateLongText(state));
                break;
            case "time":
            case "date":
                uiDiv.appendChild(this.generateDate(state));
                break;
            case "radio":
            case "checkbox":
                uiDiv.appendChild(this.generateChoice(state));
                break;

            case "dropdown":
                uiDiv.appendChild(this.generateDropdown(state));
                break;

            case "scaleGrid":
                uiDiv.appendChild(this.generateScaleGrid(state));
                break;

            case "fileUpload":
                uiDiv.appendChild(this.generateFileUpload(state));
                break;
        }
        if (state == "editor") {
            uiDiv.appendChild(this.isRequiredEditor());
            // Drag and drop functionality and delete button
            this.addEditorFunctionality(mainDIV);
        }
        // Add the form element to the container
        container.appendChild(mainDIV);
    }

    // Editor elements
    isRequiredEditor() {
        //Add switch for required
        const switchdiv = document.createElement("div");
        switchdiv.classList.add("form-check", "form-switch");

        //Add switch
        const input = document.createElement("input");
        input.type = "checkbox";
        input.classList.add("form-check-input");
        input.id = `isRequired - ${this.id}`;
        input.checked = this.required;
        switchdiv.appendChild(input);

        //Add label for switch
        const label = document.createElement("label");
        label.classList.add("form-check-label");
        label.for = `isRequired - ${this.id}}`;
        label.innerHTML = "Kötelező";
        switchdiv.appendChild(label);

        return switchdiv;
    }

    addEditorFunctionality(editor) {
        // Holder div
        const holderDiv = document.createElement('div');
        holderDiv.classList.add("nav-bar");
        editor.appendChild(holderDiv);

        // Add delete button
        const deleteButton = document.createElement('button');
        deleteButton.classList.add("btn", "btn-close", "btn-sm", "deleteButton");
        deleteButton.onclick = function () {
            formElements = formElements.filter(element =>
                `${element.type}-${element.id}` !== editor.id
            );
            everythingSaved = false;
            editor.remove();
        };
        holderDiv.appendChild(deleteButton);

        // Add drag and drop handle to the editor
        const dragHandle = document.createElement('span');
        dragHandle.classList.add("dragHandle");
        dragHandle.innerHTML = '<i class="fas fa-grip-vertical"></i>';
        holderDiv.appendChild(dragHandle);
    }

    generateEmail(state) {
        const emailInput = document.createElement('input');
        emailInput.type = 'email';
        emailInput.classList.add("form-control", "mb-3");

        emailInput.placeholder = 'Email cím';

        if (state == "editor") {
            emailInput.disabled = true;
        } else if (state == "fill" || state == "answer") {
            emailInput.classList.add("userInput");
            if (this.required) {
                emailInput.required = true;
            }
            if (state == "answer") {
                emailInput.disabled = true;
                emailInput.value = this.answer;
            }
        }
        return emailInput;
    }

    generateShortText(state) {
        const shortTextInput = document.createElement('input');
        shortTextInput.type = 'text';
        shortTextInput.classList.add("form-control", "mb-3");
        shortTextInput.placeholder = 'Rövid szöveg';

        if (state == "editor") {
            shortTextInput.disabled = true;
        } else if (state == "fill" || state == "answer") {
            shortTextInput.classList.add("userInput");
            if (this.required) {
                shortTextInput.required = true;
            }
            if (state == "answer") {
                shortTextInput.disabled = true;
                shortTextInput.value = this.answer;
            }
        }
        return shortTextInput;
    }

    generateLongText(state) {
        const longTextInput = document.createElement('textarea');
        longTextInput.classList.add("form-control", "mb-3");
        longTextInput.placeholder = 'Hosszú szöveg';

        if (state == "editor") {
            longTextInput.disabled = true;
        } else if (state == "fill" || state == "answer") {
            longTextInput.classList.add("userInput");
            if (this.required) {
                longTextInput.required = true;
            }
            if (state == "answer") {
                longTextInput.disabled = true;
                longTextInput.value = this.answer;
            }
        }
        return longTextInput;
    }

    generateDate(state) {
        const dateInput = document.createElement('input');
        dateInput.type = this.type;
        dateInput.classList.add("form-control", "mb-3");
        if (state == "editor") {
            dateInput.disabled = true;
        } else if (state == "fill" || state == "answer") {
            dateInput.classList.add("userInput");
            if (this.required) {
                dateInput.required = true;
            }
            if (state == "answer") {
                dateInput.disabled = true;
                dateInput.value = answer;
            }
        }
        return dateInput;
    }

    // Radio and checkbox
    generateChoice(state) {
        const holderDiv = document.createElement('div');
        holderDiv.classList.add("mb-3", `${this.type}-holder`);

        if (this.details?.length == 0) {
            this.details = ["", ""];
        }
        this.details?.forEach((option, index) => {
            holderDiv.appendChild(this.addNewChoiceOption(option, index, state));
        });

        if (state == "editor") {
            const addRadio = document.createElement("button");
            addRadio.classList.add("btn", "btn-success", "btn-sm");
            addRadio.innerHTML = "+";
            addRadio.onclick = () => {
                // Create the new choice option element
                var newChoiceOption = this.addNewChoiceOption("", holderDiv.children.length - 1, state);

                // Insert the new choice option before the last child of holderDiv (the "Add" button)
                holderDiv.insertBefore(newChoiceOption, holderDiv.lastChild);
            };
            holderDiv.appendChild(addRadio);
        }

        return holderDiv;
    }

    addNewChoiceOption(option, index, state) {
        // Create div for checkbox or radio  
        const div = document.createElement("div");
        div.classList.add(state == "editor" ? "input-group" : "form-check", "mb-2");
        div.setAttribute('data-option', index);

        if (state === "editor") {
            const CheckHolderDiv = document.createElement("div");
            CheckHolderDiv.classList.add("input-group-text");
            CheckHolderDiv.innerHTML = `<input class="form-check-input" type="${this.type === 'radio' ? 'radio' : 'checkbox'}" id="${this.id}-option-${index}" data-name="${option}" disabled>`;
            div.appendChild(CheckHolderDiv);

            const label = document.createElement("input");
            label.type = "text";
            label.classList.add("form-control", "details-text");
            label.style.marginBottom = "0";
            label.placeholder = "Opció";
            label.value = option;
            div.appendChild(label);
        } else {
            // Create input for checkbox or radio
            const checkbox = document.createElement("input");
            checkbox.type = this.type;
            checkbox.classList.add("form-check-input");
            checkbox.classList.add("userInput");
            checkbox.name = checkbox.type === "radio" ? `${this.id}-radio` : `${this.id}-checkbox`;
            if (state === "answer") {
                checkbox.disabled = true;
                if (this.answer.includes(option)) {
                    checkbox.checked = true;
                }
            }
            checkbox.setAttribute('data-name', option);
            checkbox.id = `${this.id}-option-${index}`;
            div.appendChild(checkbox);

            // Create label for checkbox or radio
            const Qlabel = document.createElement("label");
            Qlabel.classList.add("form-check-label");
            Qlabel.htmlFor = `${this.id}-option-${index}`;
            Qlabel.textContent = option ? option : "Opció";
            div.appendChild(Qlabel);
        }

        if (state === "editor") {
            const deleteHolder = document.createElement("div");
            deleteHolder.classList.add("input-group-text");

            // Create delete button
            const deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-close", "btn-sm");
            deleteButton.onclick = function () {
                div.remove();
            };
            deleteHolder.appendChild(deleteButton);
            div.appendChild(deleteHolder);
        }
        return div;
    }

    // Dropdown
    generateDropdown(state) {
        //Create dropdown holder
        const dropdownHolder = document.createElement("div");
        dropdownHolder.classList.add("dropdown-holder", "mb-3");

        let dropdownButton;
        if (state == "fill" || state == "answer") {
            dropdownButton = document.createElement("select");
            dropdownButton.classList.add("form-select", "userInput");
            dropdownButton.id = `${this.id}-dropdown`;
            dropdownHolder.appendChild(dropdownButton);
            if (state == "answer") {
                dropdownButton.disabled = true;
            }
        }

        if (this.details?.length == 0) {
            this.details = ["", ""];
        }
        this.details?.forEach((option, index) => {
            if (state == "fill" || state == "answer") {
                dropdownButton.appendChild(this.addNewDropdownOption(option, index, state));
            } else if (state == "editor") {
                dropdownHolder.appendChild(this.addNewDropdownOption(option, index, state));
            }
        });

        if (state == "answer") {
            const optionToSelect = Array.from(dropdownButton.options).find(option => option.value === this.answer);
            if (optionToSelect) {
                optionToSelect.selected = true;
            }
        }


        if (state == "editor") {
            const addDropdown = document.createElement("button");
            addDropdown.classList.add("btn", "btn-success", "btn-sm");
            addDropdown.innerHTML = "+";
            addDropdown.onclick = () => {
                // Create the new choice option element
                var newChoiceOption = this.addNewDropdownOption("", dropdownHolder.children.length - 1, state);

                // Insert the new choice option before the last child of holderDiv (the "Add" button)
                dropdownHolder.insertBefore(newChoiceOption, dropdownHolder.lastChild);
            };
            dropdownHolder.appendChild(addDropdown);
        }

        return dropdownHolder;
    }

    addNewDropdownOption(option, index, state) {
        if (state == "editor") {
            //Create div for dropdown
            const div = document.createElement("div");
            div.classList.add("form-check");
            div.setAttribute('data-option', index);

            //Create arrow
            const arrow = document.createElement("i");
            arrow.classList.add("fas", "fa-arrow-right", "fa-lg");
            div.appendChild(arrow);

            //Create option for dropdown
            const label = document.createElement("input");
            label.type = "text";
            label.classList.add("form-control", "details-text");
            label.placeholder = "Opció";
            label.value = option ? option : "";
            div.appendChild(label);

            //Create delete button
            const deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-close", "btn-sm");
            deleteButton.onclick = function () {
                div.remove();
            };
            div.appendChild(deleteButton);
            return div;

        } else if (state == "fill" || state == "answer") {
            const dropdown = document.createElement("option");
            dropdown.value = option ? option : "Opció";
            dropdown.innerHTML = option ? option : "Opció";
            //dropdown.classList.add("userInput");
            return dropdown;
        }
    }

    // Scale grid
    generateScaleGrid(state) {

        if (state == "editor") {
            return this.generateScaleGridEditor();
        }

        const columns = this.options.scale ? this.options.scale : 4;

        // Create scale grid holder
        const gridHolder = document.createElement("div");
        gridHolder.classList.add("container", "justify-content-center");
        gridHolder.classList.add("grid-holder");
        gridHolder.style.paddingLeft = "10px";
        gridHolder.style.paddingRight = "10px";

        //Create header row
        const headerRow = document.createElement("div");
        headerRow.classList.add("row");
        headerRow.style.flexWrap = "nowrap";
        headerRow.setAttribute('data-option', "header");


        const spacerColumn = document.createElement("div");
        spacerColumn.classList.add("col-3");
        spacerColumn.style.minWidth = "50px";
        headerRow.appendChild(spacerColumn);

        Array.from({ length: columns + 1 }).forEach((column, index) => {
            const headerColumn = document.createElement("div");
            headerColumn.classList.add("col", "header-column", "text-center");
            headerColumn.style.minWidth = "50px";
            headerColumn.innerHTML = index + 1;
            headerRow.appendChild(headerColumn);
        });
        gridHolder.appendChild(headerRow);


        if (this.details?.length == 0) {
            this.details = ["", ""];
        }
        //Create rows
        this.details?.forEach((option, index) => {
            gridHolder.appendChild(this.addNewRow(option, index, state, columns));
        });

        return gridHolder;
    }

    addNewRow(option, index, state, columns) {
        //Create row
        const row = document.createElement("div");
        row.classList.add("row", "mb-3");
        row.style.flexWrap = "nowrap";
        row.setAttribute('data-option', index);
        row.classList.add("grid-row");

        //Create label for row

        const labelHolder = document.createElement("div");
        labelHolder.classList.add("col-3");
        labelHolder.appendChild(this.createRowLabel(option, state));
        row.appendChild(labelHolder);


        //Create columns with radio buttons
        Array.from({ length: columns + 1 }).forEach((_, j) => {
            const column = document.createElement("div");
            column.classList.add("col", "text-center", "align-self-center");

            const input = document.createElement("input");
            input.type = "radio";
            input.classList.add("form-check-input");
            //input.id = id + "-" + rownum + "-" + j;
            input.name = `${this.id}-scaleGrid-${index}`;

            input.classList.add("userInput");
            if (state == "answer") {
                input.disabled = true;
                if (this.answer[index].answers[j] == '1') {
                    input.checked = true;
                }
            }

            column.appendChild(input);

            row.appendChild(column);
        });
        return row;
    }

    createRowLabel(option, state) {
        //Create input for row
        if (state == "editor") {
            const div = document.createElement("div");
            div.classList.add("mb-2", "d-flex", "no-wrap", "align-items-center");

            const input = document.createElement("input");
            input.type = "text";
            input.classList.add("form-control", "details-text");
            input.placeholder = "Sor";
            input.value = option ? option : "";

            const deleteButton = document.createElement("button");
            deleteButton.classList.add("btn", "btn-close", "btn-sm");
            deleteButton.onclick = function () {
                div.remove();
            }

            div.appendChild(input);
            div.appendChild(deleteButton);
            return div;
        } else if (state == "fill" || state == "answer") {
            const label = document.createElement("label");
            label.classList.add("form-check-label", "row-label");
            label.innerHTML = option ? option : "Opció";
            label.style.textAlign = "center";
            return label;
        }
    }

    createOptionLabel(option) {
        const div = document.createElement("div");
        div.classList.add("mb-2", "d-flex", "no-wrap", "align-items-center");

        const input = document.createElement("input");
        input.type = "text";
        input.classList.add("form-control", "options-text");
        input.placeholder = "Oszlop";
        input.value = option ? option : "";

        const deleteButton = document.createElement("button");
        deleteButton.classList.add("btn", "btn-close", "btn-sm");
        deleteButton.onclick = function () {
            div.remove();
        };


        div.appendChild(input);
        div.appendChild(deleteButton);
        return div;
    }

    generateScaleGridEditor() {
        // Create scale grid holder
        const gridHolder = document.createElement("div");
        gridHolder.classList.add("container", "justify-content-center");
        gridHolder.classList.add("grid-holder");
        gridHolder.style.paddingLeft = "10px";
        gridHolder.style.paddingRight = "10px";

        //Create header row with two columns
        const headerRow = document.createElement("div");
        headerRow.classList.add("row");
        headerRow.style.flexWrap = "nowrap";
        headerRow.setAttribute('data-option', "header");

        // First column for labels
        const labelColumn = document.createElement("div");
        labelColumn.classList.add("col");
        labelColumn.innerHTML = "Sorok";
        headerRow.appendChild(labelColumn);

        if (this.details?.length == 0) {
            this.details = ["", ""];
        }
        //Create rows
        this.details?.forEach((option, index) => {
            labelColumn.appendChild(this.createRowLabel(option, "editor"));
        });

        // Second column for options
        const optionColumn = document.createElement("div");
        optionColumn.classList.add("col");
        optionColumn.innerHTML = "Oszlopok";
        headerRow.appendChild(optionColumn);

        if (this.options?.length == 0) {
            this.options = ["", ""];
        }
        //Create rows
        this.options?.forEach((option, index) => {
            optionColumn.appendChild(this.createOptionLabel(option));
        });

        gridHolder.appendChild(headerRow);

        //Create add row button

        const plusHolder = document.createElement("div");
        plusHolder.classList.add("row", "mb-3", "justify-content-center");

        const addRow = document.createElement("button");
        addRow.classList.add("btn", "btn-success", "btn-sm", "w-50");
        addRow.innerHTML = "+";
        addRow.onclick = () => {
            // Create the new choice option element
            var newChoiceOption = this.addNewRow("", holderDiv.children.length - 1, state, columns);

            // Insert the new choice option before the last child of holderDiv (the "Add" button)
            gridHolder.insertBefore(newChoiceOption, dropdownHolder.lastChild);
        };
        plusHolder.appendChild(addRow);
        gridHolder.appendChild(plusHolder);


        return gridHolder;
    }

    // File upload
    generateFileUpload(state) {
        const fileInput = document.createElement("input");
        fileInput.type = "file";
        fileInput.classList.add("form-control");

        if (state == "editor") {
            fileInput.disabled = true;
        } else if (state == "fill") {
            fileInput.classList.add("userInput");
            if (this.required) {
                fileInput.required = true;
            }
        }
        return fileInput;
    }

    // Setters
    setAnswer(answer) {
        this.answer = answer;
    }

    loadUserinput() {
        if (this.type === 'email' || this.type === 'shortText' || this.type === 'longText' || this.type === 'date') {
            document.getElementById(`${this.type}-${this.id}`).querySelector('input').value = this.answer;
        }
        else if (this.type === 'radio' || this.type === 'checkbox') {
            document.querySelectorAll(`#${this.type}-${this.id} .userInput`).forEach(input => {
                const dataName = input.getAttribute('data-name');
                if (this.type === 'radio') {
                    // For radio buttons, only one option should be selected
                    input.checked = dataName === this.answer[0];
                } else if (this.type === 'checkbox') {
                    // For checkboxes, multiple options can be selected
                    input.checked = this.answer.includes(dataName);
                }
            });
        } else if (this.type === 'dropdown') {
            const selectElement = document.querySelector(`#${this.type}-${this.id} .userInput`);
            if (selectElement) {
                const optionToSelect = Array.from(selectElement.options).find(option => option.value === this.answer);
                if (optionToSelect) {
                    optionToSelect.selected = true;
                }
            }
        }
        else if (this.type === 'scaleGrid') {
            document.querySelectorAll(`#${this.type}-${this.id} .grid-row`).forEach((row, i) => {
                row.querySelectorAll('.userInput').forEach((input, j) => {
                    input.checked = this.answer[i].answers[j];
                });
            });
        }
    }

    // Getters
    getQuestion() {
        return document.getElementById(`${this.type}-${this.id}`).querySelector('input[type="text"]').value;
    }

    getRequired() {
        return document.getElementById(`isRequired - ${this.id}`).checked;
    }

    getDetails() {
        if (this.type === 'radio' || this.type === 'checkbox' || this.type === 'dropdown' || this.type === 'scaleGrid') {
            const details = [];
            document.querySelectorAll(`#${this.type}-${this.id} .details-text`).forEach(input => {
                details.push(input.value);
            });
            this.details = details;
            return details;
        } else {
            return "";
        }
    }

    getOptions() {
        if (this.type === 'scaleGrid') {
            const options = [];
            document.querySelectorAll(`#${this.type}-${this.id} .options-text`).forEach(input => {
                options.push(input.value);
            });
            this.options = options;
            return options;
        } else {
            return "";
        }
    }

    getAnswer() {
        if (this.type === 'email' || this.type === 'shortText' || this.type === 'longText' || this.type === 'date') {
            return document.getElementById(`${this.type}-${this.id}`).querySelector('input').value;
        } else if (this.type === 'radio' || this.type === 'checkbox') {
            const answer = [];
            document.querySelectorAll(`#${this.type}-${this.id} .userInput`).forEach(input => {
                if (input.checked) {
                    answer.push(input.getAttribute('data-name'));
                }
            });
            return answer;
        } else if (this.type === 'dropdown') {
            return document.querySelector(`#${this.type}-${this.id} .userInput`).value
        } else if (this.type === 'scaleGrid') {
            const answer = [];
            document.querySelectorAll(`#${this.type}-${this.id} .grid-row`).forEach(row => {
                const rowAnswer = [];
                row.querySelectorAll('.userInput').forEach(input => {
                    rowAnswer.push(input.checked ? 1 : 0);
                });
                answer.push({ answers: rowAnswer });
            });
            return answer;
        }
    }
}


async function loadPage(form, state) {

    const formData = JSON.parse(form.Data);
    const formName = form.Name;
    const formStatus = form.Status;
    const formAccess = form.AccessRestrict;
    const formAnonim = form.Anonim;
    const formSingleAnswer = form.SingleAnswer;
    const formHash = form.LinkHash;


    if (state == "fill") {
        //Set form Name and header
        document.getElementById("form_name").innerHTML = formName;
        document.getElementById("form_header").innerHTML = form.Header.replace(/\n/g, "<br>");

    }
    if (state == "editor") {
        //Set form Name
        document.getElementById("form_name").innerHTML = formName + '&nbsp<i class="fas fa-edit fa-xs" style="color: #747b86"></i>';
        document.getElementById("description").value = form.Header;

        //Set form state
        document.getElementById("isAcceptingAnswers").checked = formStatus;

        //Set form access
        document.getElementById("accessRestrict").value = formAccess;

        if (form.AccessRestrict == 3) {
            showLink(formHash);
        } else {
            showLink(formHash, false);
        }

        //Set form settings
        if (formAnonim == 1) {
            document.querySelector('[data-setting="Anonim"]').checked = true;
        }
        if (formSingleAnswer == 1) {
            document.querySelector('[data-setting="SingleAnswer"]').checked = true;
        }
    }

    // Set background
    const style = document.createElement('style');
    style.innerHTML = `
    body::before {
    content: "";
    position: fixed;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    background-image: url(../forms/backgrounds/${form.Background});
    background-size: cover;
    background-position: center;
    z-index: -1;
    }`;
    document.head.appendChild(style);

    if (state == "success") {
        //Set form Name and header if form is closed
        document.getElementById("form_name").innerHTML = "Sikeres leadás!";
        document.getElementById("form_header").innerHTML = "Köszönjük, hogy kitöltötte a kérdőívet!";
        return;
    }
    let formContainer = document.getElementById("form-body");
    if (state == "editor") {
        formContainer = document.getElementById("editorZone");
    }


    console.log(formData);

    formData?.forEach((element, index) => {
        let { type: type, id: id, question: question, details: details, required: required, options: options } = element;

        // Create a new form element
        let formElement = new FormElement(id, type, question, details, required, options);
        formElement.createElement(formContainer, state);
        formElements.push(formElement);
    });


    //Create form elements
    if (state == "fill") {
        //Add submit button
        const submit = document.createElement("button");
        submit.classList.add("btn", "btn-lg", "btn-success");
        submit.type = "submit";
        submit.innerHTML = "Leadás";
        formContainer.appendChild(submit);

        try {
            reloadUserInput();
        } catch (e) {
            console.log("No user input to reload");
        }
    }
}

// Serialize form elements --> Convert form elements to JSON
function serializeFormElements(formElements) {
    return formElements.map(element => {
        return {
            id: element.id,
            type: element.type,
            question: element.getQuestion("editor"),
            details: element.getDetails("editor"),
            required: element.getRequired("editor"),
            options: element.getOptions("editor"),
            //answer: element.getAnswer()
        };
    });
}


async function saveFormElements(auto) {
    // Safety check for conflicting IDs
    const formElementIds = formElements.map(element => element.id);
    if (new Set(formElementIds).size !== formElementIds.length) {
        // Fix conflicting IDs
        formElements.forEach((element, index) => {
            element.id = index;

            // Update the ID of the corresponding element in the DOM
            const elementDiv = document.getElementById(`${element.type}-${element.id}`);

            if (elementDiv) {
                elementDiv.id = `${element.type}-${element.id}`;
            }

        });
    }

    const formElementsToSave = serializeFormElements(formElements);
    const formElementsJson = JSON.stringify(formElementsToSave);

    const formHeader = document.getElementById("description").value;

    // Create a URLSearchParams object from the current window location
    const params = new URLSearchParams(window.location.search);

    // Get 'formId' parameter from the URL, default to null if not found
    const formId = params.has('formId') ? params.get('formId') : null;

    // Get 'form' parameter from the URL, default to null if not found
    const formHash = params.has('form') ? params.get('form') : null;

    let response = await $.ajax({
        type: "POST",
        url: "../formManager.php",
        data: {
            mode: "saveFormElements",
            formElements: formElementsJson,
            formId: formId,
            formHash: formHash,
            formHeader: formHeader,
        }
    });

    if (response == 200) {
        successToast(auto ? "Automatikus mentés!" : "Sikeres mentés!");
        everythingSaved = true;
    } else {
        errorToast("Sikertelen mentés!");
    }
}


// FORM SUBMISSION

function submitFormElements(formElements) {
    return formElements.map(element => {
        return {
            id: element.id,
            type: element.type,
            question: element.question,
            details: element.details,
            required: element.required == 1 ? true : false,
            options: element.options,
            answer: element.getAnswer()
        };
    });
}

async function submitAnswer(formId, formHash, isAnonim) {
    let answers = submitFormElements(formElements);
    answers = JSON.stringify(answers);

    console.log(answers);
    //return;

    //Set UID to 0 if user is not logged in
    var uid;
    var userIp;
    if (isAnonim == 0) {
        uid = await getUid();
        userIp = await getIp();
        //console.log("User: " + userIp);
    } else {
        console.log("Anonim");
        uid = 0;
        userIp = '0.0.0.0';
    }

    const response = await $.ajax({
        type: "POST",
        url: "../formManager.php",
        data: { mode: "submitAnswer", uid: uid, userIp: userIp, id: formId, formHash: formHash, answers: answers },
    });

    console.log(response);

    if (response == 500) {
        alert("Nem megengedett karakterek a válaszban!");
    } else if (response == 200) {
        clearUserCookie();
        if (formId != -1) {
            window.location.href = "viewform.php?formId=" + formId + "&success";
        } else {
            window.location.href = "viewform.php?form=" + formHash + "&success";
        }
    } else {
        alert("Sikertelen leadás");
    }
}
