
function changeProjectSettingsButton(projectID) {
    let settingsButton = document.createElement("button");
    settingsButton.classList.add("btn", "settingsButton");
    settingsButton.innerHTML = "<i class='fas fa-cog'></i>";
    settingsButton.onclick = function () {
        openSettings(projectID);
    }
    return settingsButton;
}



function editDescriptionButton(projectID) {
    let editDescription = document.createElement("button");
    editDescription.classList.add("btn", "editDescription");
    editDescription.innerHTML = "<i class='fas fa-pencil-alt' style='color: #585d65;'></i>";
    editDescription.onclick = function () {
        editProjectDescription(projectID);
    }
    return editDescription;
}

async function editProjectDescription(proj_id) {

    // Fetch the project settings
    var projectSettings = await fetchProject(proj_id);

    // Set the project description
    var projectDescription = projectSettings.Description;
    document.getElementById("projectDescription").value = projectDescription;

    // Create save button
    var saveButton = document.getElementById("saveDescButton");
    saveButton.onclick = function () {
        saveProjectDescription(proj_id);
    }

    $('#projectDescModal').modal('show');
}

async function saveProjectDescription(proj_id) {

    // Get the project description
    var projectDescription = document.getElementById("projectDescription").value;

    // Save the project description
    var response = await saveProjectDescriptionToDB(proj_id, projectDescription);

    if (response == 500) {
        console.error("Error: 500");
        return;
    }

    // Set the new description
    let project = document.getElementById(proj_id);
    project.querySelector(".projectDescription").innerHTML = projectDescription;

    // Close the modal
    $('#projectDescModal').modal('hide');

}


function removeMemberFromProjectButton(projectID, memberID) {
    let removeMemberButton = document.createElement("button");
    removeMemberButton.classList.add("btn", "removeMemberButton");
    removeMemberButton.innerHTML = "<i class='fas fa-user-minus'></i>";
    removeMemberButton.style.color = "#ff0000";
    removeMemberButton.onclick = async function () {
        const response = await removeMemberFromProject(projectID, memberID);
        if (response == 200) {
            this.parentElement.parentElement.remove();
        } else if (response == 500) {
            console.error("Error: 500");
            return;
        } else if (response == 403) {
            console.error("Ejnye ilyet nem szabad!");
            return;
        }
    }
    return removeMemberButton;
}

async function removeMemberFromProject(projectID, memberID) {
    console.log("Removing member from project");
    console.log("Project ID:", projectID);
    console.log("Member ID:", memberID);

    return await $.ajax({
        type: "POST",
        url: "../projectManager.php",
        data: { mode: "removeMemberFromProject", projectId: projectID, userId: memberID },
    });
}

async function editProjectMembersButton(projectID) {
    let addMember = document.createElement("button");
    addMember.classList.add("btn", "btn-secondary", "btn-lg", "mb-2");
    addMember.innerHTML = "<i class='far fa-edit'></i>";
    addMember.onclick = function () {
        editProjectMembers(projectID);
    }
    return addMember;
}


async function editProjectMembers(projectID) {
    console.log("Adding new member to project: " + projectID);

    $('#addMemberModal').modal('show');

    // Load project members
    var projectMembers = await fetchProjectMembers(projectID);
    projectMembers = JSON.parse(projectMembers);
    projectMembers = projectMembers.map(member => member.UserID);

    var membersList = await getUsers();
    membersList = JSON.parse(membersList);

    var members = document.getElementById("projectMembersSelect");
    members.innerHTML = "";
    for (let i = 0; i < membersList.length; i++) {
        var member = membersList[i];

        var option = document.createElement("div");
        option.classList.add("availableMember");
        option.style.cursor = "pointer";
        option.id = member.idUsers;
        option.innerHTML = member.lastName + " " + member.firstName;
        option.onclick = function () {
            if (this.classList.contains("selectedMember")) {
                this.classList.remove("selectedMember");
            }
            else {
                this.classList.add("selectedMember");
            }
        }

        if (projectMembers && projectMembers.includes(member.idUsers.toString())) {
            option.classList.add("selectedMember");
        }

        members.appendChild(option);
    }

    // Create save button
    var saveButton = document.getElementById("saveProjectMembers");
    saveButton.onclick = function () {
        saveProjectMemberSettings(projectID);
    }
}


async function saveProjectMemberSettings(projectID) {
    var members = document.getElementsByClassName("selectedMember");
    var projectMembers = [];
    for (let i = 0; i < members.length; i++) {
        projectMembers.push(members[i].id);
    }

    console.log(projectMembers);
    var response = await saveProjectMembersToDB(projectID, projectMembers);

    if (response == 500) {
        console.error("Error: 500");
        return;
    } else if (response == 200) {
        location.reload();
    }

    // Close the modal
    $('#addMemberModal').modal('hide');

}


// Save project settings

async function saveProjectSettings(proj_id) {

    // Get the project name
    var projectName = document.getElementById("projectName").value;

    // Get the project deadline
    var projectDate = document.getElementById("projectDate").value;
    var projectTime = document.getElementById("projectTime").value;

    // Combine the date and time
    let projectDeadline = "NULL";

    if (projectDate && projectTime) {
        projectDeadline = projectDate + " " + projectTime;
    } else if (projectDate) {
        projectDeadline = projectDate + " 23:59:59";
    }

    // Get the project visibility
    var projectVisibility = document.getElementById("projectVisibility").value;

    // Save the project settings
    var response = await saveProjectSettingsToDB(proj_id, projectName, projectDeadline, projectVisibility);

    if (response == 500) {
        console.error("Error: 500");
        return;
    } else if (response == 200) {
        location.reload();
    }

    // Close the modal
    $('#projectSettingsModal').modal('hide');


}




// Delete project

async function deleteProject(proj_id) {

    // Get the project name
    var projectName = document.getElementById("deleteText").placeholder;

    var typedName = document.getElementById("deleteText").value;

    if (projectName == typedName) {

        // Delete the project
        await deleteProjectFromDB(proj_id);

        // Close the modal
        $('#projectSettingsModal').modal('hide');

        // Reload the page
        location.reload();

    } else {
        console.error("Error: Project name does not match");
        return;
    }

}


async function archiveProject(projectID) {
    console.log("Archiving project: " + projectID);

    document.getElementById('deleteTaskSure').innerHTML = "Archiválás";
    document.getElementById('deleteTaskSure').classList.remove("btn-danger");
    document.getElementById('deleteTaskSure').classList.add("btn-warning");



    // Create a new Promise that resolves when the button is clicked
    let buttonClicked = new Promise((resolve, reject) => {
        document.getElementById('deleteTaskSure').addEventListener('click', resolve);
        document.getElementById('cancelButton').addEventListener('click', reject);
    });

    buttonClicked.then(async () => {
        // Archive the project
        $.ajax({
            type: "POST",
            url: "../../projectManager.php",
            data: { mode: "archiveProject", projectId: projectID },
            success: function (response) {
                console.log(response);
                if (response == 500) {
                    window.location.href = "index.php?serverError";
                }
                if (response == 200) {
                    location.reload();
                }
            }
        });
    }).catch(() => {
        // Do nothing
        console.log("Archiving cancelled");
        return;
    });


}