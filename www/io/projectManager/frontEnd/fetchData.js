
async function checkForUpdates(lastUpdate) {

    // Make lastUpdate to a yyyy:mm:dd hh:mm:ss format from JS Date object
    lastUpdate = new Date(lastUpdate);
    lastUpdate = lastUpdate.toISOString().slice(0, 19).replace("T", " ");


    const response = await $.ajax({
        type: "POST",
        url: "../../projectManager.php",
        data: {
            mode: "checkForUpdates",
            lastUpdate: lastUpdate
        }
    });

    if (response == 500) {
        serverErrorToast();
    }
    //console.log(response);

    return response;
}



async function fetchProjects(archived = 0) {
    //console.log("Fetching projects");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "listProjects", archived: archived }
            });


            if (response == 500) {
                serverErrorToast();
            }

            //console.log(response);

            var projects = JSON.parse(response);

            //console.log(projects);

            resolve(projects);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function fetchProject(proj_id) {
    //console.log("Fetching project");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "getProject", id: proj_id }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            response = JSON.parse(response);
            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function createNewProject() {
    console.log("Creating project");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "createNewProject" }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            //console.log(response);

            location.reload();

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function fetchProjectRoot(proj_id) {
    //console.log("Fetching project root");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "getProjectRoot", id: proj_id }
            });

            if (response == 500) {
                serverErrorToast();
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}


// FETCH PROJECT TASKS

async function fetchTask(proj_id = null, task_id = null, fillOut = false) {
    //console.info("Loading project tasks...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "getProjectTask", proj_id: proj_id, task_id: task_id, fillOut: fillOut }
            });

            /*  if (response != 200) {
                 serverErrorToast();
             } */

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}


// FETCH PROJECT USER INTERACTIONS

async function fetchUIs(task_id) {
    //console.info("Loading user interactions...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "getUIs", id: task_id }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function fetchUI(task_id) {
    //console.info("Loading user interaction...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "getUI", ID: task_id }
            });

            if (response == 500) {
                serverErrorToast();
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function userTaskData(task_id, type = "card", proj_id = null) {
    //console.info("Loading user task data...");

    return new Promise(async (resolve, reject) => {
        try {
            let response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: {
                    mode: "getUserTaskData",
                    task_id: task_id,
                    proj_id: proj_id,
                    type: type
                }
            });

            if (response == 500) {
                serverErrorToast();
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

// SAVE TASKS

async function saveTaskToDB(task, taskMembersArray, image, task_id = null) {
    console.info("Saving task...");

    var taskJson = JSON.stringify(task);
    var taskMembers = JSON.stringify(taskMembersArray);

    //console.log(taskJson);

    return new Promise(async (resolve, reject) => {
        try {

            let formData = new FormData();
            formData.append("mode", "saveTask");
            formData.append("task", taskJson);
            formData.append("taskMembers", taskMembers);
            if (image != null) {
                formData.append("image", image);
            }
            if (task_id != null) {
                formData.append("ID", task_id);
            }

            const response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: formData,
                processData: false,
                contentType: false
            });

            console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function submitTaskToDB(task_id, taskData) {
    console.info("Submitting task...");


    var taskJson = JSON.stringify(taskData);

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "submitTask", ID: task_id, task: taskJson }
            });

            if (response != 200) {
                serverErrorToast();
            }

            console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function deleteTaskFromDB(task_id) {
    console.info("Deleting task...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "deleteTask", ID: task_id }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

// SAVE PROJECT SETTINGS

async function saveProjectSettingsToDB(proj_id, projectName, projectDescription, projectDeadline, projectVisibility) {
    console.info("Saving project settings...");

    var settings = {
        "Name": projectName,
        "Description": projectDescription,
        "Members": "",
        "Deadline": projectDeadline,
        "Visibility_group": projectVisibility
    };

    console.log(settings);

    var settingsJson = JSON.stringify(settings);

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "saveProjectSettings", id: proj_id, settings: settingsJson }
            });

            if (response == 403) {
                noAccessToast();
            } else if (response != 200) {
                serverErrorToast();
            }

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}


async function saveProjectMembersToDB(proj_id, members) {
    console.info("Saving project members...");

    var membersJson = JSON.stringify(members);

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "saveProjectMembers", id: proj_id, Members: membersJson }
            });

            if (response == 500) {
                serverErrorToast();
            }

            console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });

}


// DELETE PROJECT

async function deleteProjectFromDB(proj_id) {
    console.info("Deleting project...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "deleteProject", id: proj_id }
            });

            if (response == 500) {
                serverErrorToast();
            }

            //console.log(response);

            location.reload();

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}


// GET PROJECT MEMBERS

async function getUsers() {
    //console.info("Loading available members...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "getUsers" }
            });

            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

async function fetchMemberNames(memberIDs) {
    //console.info("Loading member names...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            var members = [];

            for (let i = 0; i < memberIDs.length; i++) {
                let memberID = memberIDs[i];

                response = await $.ajax({
                    type: "POST",
                    url: "../../projectManager.php",
                    data: { mode: "getUsers", ID: memberID }
                });

                if (response == 500) {
                    serverErrorToast();
                }

                var member = JSON.parse(response)[0];

                members.push(member);
            }

            //console.log(members);

            resolve(members);
        } catch (error) {
            console.error("Error:", error);
            serverErrorToast();
            reject(error);
        }
    });
}

async function fetchProjectMembers(proj_id) {
    //console.info("Loading project members...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "getProjectMembers", id: proj_id }
            });

            if (response == 500) {
                serverErrorToast();
            }

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}

// GET TASK MEMBERS

async function fetchTaskMembers(task_id, proj_id) {
    //console.info("Loading task members...");

    return new Promise(async (resolve, reject) => {
        try {

            let response = await $.ajax({
                type: "POST",
                url: "../../projectManager.php",
                data: { mode: "getTaskMembers", task_id: task_id, proj_id: proj_id }
            });

            //console.log(response);

            resolve(response);
        } catch (error) {
            console.error("Error:", error);
            reject(error);
        }
    });
}