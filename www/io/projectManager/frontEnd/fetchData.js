async function fetchProjects() {
    console.log("Fetching projects");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "listProjects" }
            });


            if (response == 500) {
                window.location.href = "index.php?serverError";
            }

            var projects = JSON.parse(response);

            //console.log(projects);

            resolve(projects);
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
                url: "../projectManager.php",
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


// FETCH PROJECT SETTINGS

async function fetchProjectSettings(proj_id) {
    console.info("Loading project settings...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "getProjectSettings", id: proj_id }
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

// FETCH PROJECT TASKS

async function fetchTasks(proj_id) {
    console.info("Loading project tasks...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "getProjectTasks", id: proj_id }
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

async function fetchTask(task_id) {
    console.info("Loading task...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "getTask", ID: task_id }
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

// SAVE TASKS

async function createNewTaskDB(task) {
    console.info("Saving task...");

    var taskJson = JSON.stringify(task);

    console.log(taskJson);

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "createNewTask", task: taskJson }
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
async function saveTaskToDB(task_id, taskName, taskDeadline, taskData) {
    console.info("Saving task...");

    var task = {
        "Task_title": taskName,
        "Task_data": taskData,
        "Deadline": taskDeadline
    };

    var taskJson = JSON.stringify(task);

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "saveTask", ID: task_id, task: taskJson }
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

// SAVE PROJECT SETTINGS

async function saveProjectSettingsToDB(proj_id, projectName, projectDeadline, projectVisibility) {
    console.info("Saving project settings...");

    var settings = {
        "Name": projectName,
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
                url: "../projectManager.php",
                data: { mode: "saveProjectSettings", id: proj_id, settings: settingsJson }
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


async function saveProjectDescriptionToDB(proj_id, projectDescription) {
    console.info("Saving project description...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "saveDescription", id: proj_id, description: projectDescription }
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

async function saveProjectMembersToDB(proj_id, members) {
    console.info("Saving project members...");

    var membersJson = JSON.stringify(members);

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "saveProjectMembers", id: proj_id, Members: membersJson }
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


// DELETE PROJECT

async function deleteProjectFromDB(proj_id) {
    console.info("Deleting project...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "deleteProject", id: proj_id }
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


// GET PROJECT MEMBERS

async function getUsers() {
    console.info("Loading available members...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
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

async function fetchProjectMembers(proj_id) {
    console.info("Loading project members...");

    return new Promise(async (resolve, reject) => {
        try {
            let response;

            response = await $.ajax({
                type: "POST",
                url: "../projectManager.php",
                data: { mode: "getProjectMembers", id: proj_id }
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