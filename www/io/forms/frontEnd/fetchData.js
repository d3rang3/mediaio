async function FetchData(formId, formHash) {
    console.log("Fetching form data");

    return new Promise(async (resolve, reject) => {
        try {
            let response;
            if (formId != -1) {
                response = await $.ajax({
                    type: "POST",
                    url: "../formManager.php",
                    data: { mode: "getForm", id: formId }
                });
            } else {
                response = await $.ajax({
                    type: "POST",
                    url: "../formManager.php",
                    data: { mode: "getForm", formHash: formHash }
                });
            }

            if (response == 404) {
                window.location.href = "index.php?invalidID";
            }

            var form = JSON.parse(response);

            resolve(form);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function fetchAnswers(formId, formHash) {
    console.log("Fetching form answers");
    try {
        let data = { mode: "getFormAnswers" };
        if (formId != -1) {
            data.id = formId;
        } else {
            data.formHash = formHash;
        }

        let response = await $.ajax({
            type: "POST",
            url: "../formManager.php",
            data: data
        });

        if (response == 404) {
            window.location.href = "index.php?invalidID";
        }

        formAnswers = JSON.parse(response);

        let dropdown = document.getElementById("answers_dropdown");

        formAnswers.forEach((item, i) => {
            let id = item.ID;

            let li = document.createElement("li");
            li.classList.add("dropdown-item");
            li.style.cursor = "pointer";

            li.onclick = function () {
                showFormAnswers(id);
            };

            li.innerHTML = `${i + 1}. válasz</a>`;
            dropdown.appendChild(li);
        });
    } catch (error) {
        console.error("Error:", error);
    }
}


