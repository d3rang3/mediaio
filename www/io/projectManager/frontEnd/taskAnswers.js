
// Open task answers


async function openTaskAnswers(taskId, projectId) {

    $('#taskAnswersModal').modal('show');

    // Get the working area
    const workingArea = document.getElementById('taskAnswerData');
    workingArea.innerHTML = '';

    // Add spinner
    let spinner = document.createElement("div");
    spinner.classList.add("spinner-grow", "text-secondary");
    spinner.id = "loadingSpinner";
    spinner.style.margin = "auto";
    spinner.style.display = "block";
    workingArea.appendChild(spinner);

    // Get the task answers
    let uDataResponse = await userTaskData(taskId, "get");
    let uData = JSON.parse(uDataResponse);
    uData = uData.data ? uData.data : [];
    console.log(uData);

    // Get the task
    let task = JSON.parse(await fetchTask(null, taskId));

    // Fetch the task members
    let members = JSON.parse(await fetchTaskMembers(taskId, projectId));

    // Remove spinner
    spinner.remove();

    switch (task.Task_type) {
        case "task":
            var fillOutText = task.fillOutText;

            // Create a div for answered users
            var answeredUsers = document.createElement('div');
            answeredUsers.classList.add('memberSelect', 'mb-3');
            answeredUsers.innerHTML = `<h5>${fillOutText}:</h5>`;
            workingArea.appendChild(answeredUsers);

            // Create a div for users that haven't answered
            var notAnsweredUsers = document.createElement('div');
            notAnsweredUsers.className = 'memberSelect';
            notAnsweredUsers.innerHTML = `<h5>Nem válaszolt:</h5>`;
            workingArea.appendChild(notAnsweredUsers);

            members.forEach(member => {
                if (member.assignedToTask == 0) return;
                var memberDiv = document.createElement('div');
                memberDiv.classList.add('availableMember');
                memberDiv.innerHTML = `${member.firstName} ${member.lastName}`;
                if (uData.some(item => item.UserId === member.UserId)) {
                    var userAnswer = uData.find(item => item.UserId === member.UserId);
                    memberDiv.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="Leadva: ${userAnswer.submissionTime}">${member.firstName} ${member.lastName}</a>`;
                    // Check if the user answered late
                    userAnswer.submissionTime > task.Deadline? memberDiv.classList.add('lateSubmission') : memberDiv.classList.add('selectedMember');
                    answeredUsers.appendChild(memberDiv);
                } else {
                    notAnsweredUsers.appendChild(memberDiv);
                }
            });

            break;
        case "checklist":
        case "radio":
            var options = JSON.parse(task.Task_data);
            let checkList = options.checklist;

            // Create a div for every option
            checkList.forEach(option => {
                var optionDiv = document.createElement('div');
                optionDiv.classList.add('memberSelect', 'mb-3');
                optionDiv.setAttribute('data-option', option.value);
                optionDiv.innerHTML = `<h5>${option.value}:</h5>`;
                workingArea.appendChild(optionDiv);
            });

            // Create a div for users that haven't answered
            var notAnsweredUsers = document.createElement('div');
            notAnsweredUsers.className = 'memberSelect';
            notAnsweredUsers.innerHTML = `<h5>Nem válaszolt:</h5>`;
            workingArea.appendChild(notAnsweredUsers);

            members.forEach(member => {
                if (member.assignedToTask == 0) return;
                var memberDiv = document.createElement('div');
                memberDiv.classList.add('availableMember');
                memberDiv.innerHTML = `${member.firstName} ${member.lastName}`;
                if (uData.some(item => item.UserId === member.UserId)) {
                    var userAnswer = uData.find(item => item.UserId === member.UserId);
                    memberDiv.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="Leadva: ${userAnswer.submissionTime}">${member.firstName} ${member.lastName}</a>`;
                    // Check if the user answered late
                    userAnswer.submissionTime > task.Deadline? memberDiv.classList.add('lateSubmission') : memberDiv.classList.add('selectedMember');
                    userAnswer = JSON.parse(userAnswer.Data);
                    userAnswer.forEach(answer => {
                        if (answer.checked == false) return;
                        var optionDiv = workingArea.querySelector(`div[data-option="${answer.value}"]`);
                        if (!optionDiv) return;
                        var clonedMemberDiv = memberDiv.cloneNode(true); // clone the memberDiv
                        optionDiv.appendChild(clonedMemberDiv);
                    });

                } else {
                    notAnsweredUsers.appendChild(memberDiv);
                }
            });

            break;

    }
    toolTipRender();
}