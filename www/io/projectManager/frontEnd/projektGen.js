

function generateProjects(project, mobile = false) {
    return new Promise(async (resolve, reject) => {
        try {
            const container = document.getElementsByClassName("container")[0];
            if (mobile) {
                // Generating accordion
                let accordion = document.createElement("div");
                accordion.classList.add("accordion");
                accordion.id = "accordion";

                container.innerHTML = "";
                container.appendChild(accordion);

                await Promise.all(project.map(p => generateMobileProjectBody(p, accordion)));
            } else {
                await Promise.all(project.map(generateProjectBody));
            }

            await generateNewProjectButton(mobile);
        } catch (error) {
            console.error(error);
        }
        resolve();
    });
}


async function generateMobileView(project) {
    
    let projectName = project.Name;
    let projectID = project.ID;

    // Create a new project holder card
    let projectCard = document.getElementsByClassName("projectHolder")[0];
    projectCard.id = projectID;
    projectCard.style.flexDirection = "column";
    //colorCardBasedOnDeadline(projectCard, project.Deadline);


    // Create nav
    let nav = document.createElement("ul");
    nav.classList.add("nav", "nav-underline", "justify-content-center");

    // Add back button
    let backButton = document.createElement("button");
    backButton.classList.add("btn");
    backButton.innerHTML = '<i class="fas fa-compress-alt fa-lg"></i>';
    backButton.onclick = function () {
        window.location.href = "index.php";
    }
    nav.appendChild(backButton);

    // Create li elements
    let tasks = document.createElement("li");
    tasks.classList.add("nav-item");
    tasks.innerHTML = `<button class='nav-link active' id='task-tab' data-bs-toggle='tab' data-bs-target='#task-tab-pane-${projectID}' type='button' role='tab' aria-controls='task-tab-pane' aria-selected='true'>${projectName}</button>`;
    nav.appendChild(tasks);

    let members = document.createElement("li");
    members.classList.add("nav-item");
    members.innerHTML = "<button class='nav-link' id='users-tab' data-bs-toggle='tab' data-bs-target='#users-tab-pane-" + projectID + "' type='button' role='tab' aria-controls='users-tab-pane' aria-selected='false'>Tagok</button>";
    nav.appendChild(members);

    if (project.Deadline) {
        let deadline = document.createElement("li");
        deadline.classList.add("nav-item");
        deadline.innerHTML = "<a class='nav-link disabled' aria-disabled='true'><b>" + getDeadline(project.Deadline) + "</b></a>";
        nav.appendChild(deadline);
    }

    projectCard.appendChild(nav);
    
    // Add settings button to project title
    try {
        nav.appendChild(changeProjectSettingsButton(projectID));
    } catch (error) {
        ;
    }
    
    // Create a nav div
    let navDiv = document.createElement("div");
    navDiv.classList.add("tab-content");
    projectCard.appendChild(navDiv);

    // Create a new project body
    let projectBody = document.createElement("div");
    projectBody.classList.add("projectBody", "tab-pane", "fade", "show", "active");
    projectBody.id = "task-tab-pane-" + projectID;
    projectBody.role = "tabpanel";
    projectBody.ariaLabelledby = "task-tab";
    navDiv.appendChild(projectBody);


    // Create a new project description
    projectBody.appendChild(createDiscription(projectID, project.Description));
    
    // Generating the project tasks
    projectBody.appendChild(await generateTasks(projectID, project.canEdit));

    if (project.canEdit) {
        let addTask = document.createElement("button");
        addTask.classList.add("btn", "btn-success", "dropdown-toggle", "addTask");
        addTask.innerHTML = "Új hozzáadása";
        addTask.setAttribute("data-bs-toggle", "dropdown");
        projectBody.appendChild(addTask);

        // Creat ul dropdown
        let ul = document.createElement("ul");
        ul.classList.add("dropdown-menu");
        projectBody.appendChild(ul);

        // create li elements
        let task = document.createElement("li");
        task.classList.add("dropdown-item");
        task.innerHTML = "<i class='fas fa-stream fa-sm'></i> Feladat";
        task.style.cursor = "pointer";
        task.onclick = function () {
            addNewTask(projectID, "task", project.Deadline);
        }
        ul.appendChild(task);

        let checklist = document.createElement("li");
        checklist.classList.add("dropdown-item");
        checklist.innerHTML = "<i class='fas fa-list fa-sm'></i> Lista";
        checklist.style.cursor = "pointer";
        checklist.onclick = function () {
            addNewTask(projectID, "checklist");
        }
        ul.appendChild(checklist);

        let radio = document.createElement("li");
        radio.classList.add("dropdown-item");
        radio.innerHTML = "<i class='fas fa-dot-circle fa-sm'></i> Választós lista";
        radio.style.cursor = "pointer";
        radio.onclick = function () {
            addNewTask(projectID, "radio");
        }
        ul.appendChild(radio);
    }
    // Create Body for members
    let membersBody = document.createElement("div");
    membersBody.classList.add("card-body", "projectBody", "tab-pane", "fade");
    membersBody.id = "users-tab-pane-" + projectID;
    membersBody.role = "tabpanel";
    membersBody.ariaLabelledby = "users-tab";
    navDiv.appendChild(membersBody);


    try {
        // Add + button to add new members
        membersBody.appendChild(await editProjectMembersButton(projectID));
    } catch (error) {
        ;
    }
    // Adding members to the project body
    membersBody.appendChild(await generateMembers(projectID));
    
}
// Function to generate a mobile project list

async function generateMobileProjectBody(project, accordion) {
    // Generating item
    let item = document.createElement("div");
    item.classList.add("accordion-item");
    accordion.appendChild(item);

    // Generating header
    let header = document.createElement("h2");
    header.classList.add("accordion-header");
    header.id = "flush-heading" + project.ID;

    // Generating button
    let headerButton = document.createElement("button");
    headerButton.classList.add("accordion-button", "collapsed");
    headerButton.type = "button";
    headerButton.setAttribute("data-bs-toggle", "collapse");
    headerButton.setAttribute("data-bs-target", "#flush-collapse" + project.ID);
    headerButton.setAttribute("aria-expanded", "false");
    headerButton.innerHTML = project.Name;
    if (project.Deadline) {
        colorTaskCard(headerButton, project.Deadline);
    }
    header.appendChild(headerButton);

    item.appendChild(header);

    // Generating collapse
    let collapse = document.createElement("div");
    collapse.classList.add("accordion-collapse", "collapse");
    collapse.id = "flush-collapse" + project.ID;
    collapse.setAttribute("aria-labelledby", "flush-heading" + project.ID);
    collapse.setAttribute("data-bs-parent", "#accordionFlushExample");
    item.appendChild(collapse);

    // Generating body
    let body = document.createElement("div");
    body.classList.add("accordion-body");
    collapse.appendChild(body);

    // Generate tasks inside the project
    let description = document.createElement("p");
    description.innerHTML = makeFormatting(project.Description);
    body.appendChild(description);

    // Project deadline
    if (project.Deadline) {
        let deadline = document.createElement("p");
        deadline.innerHTML = "<b>Határidő: </b>" + await getDeadline(project.Deadline);
        body.appendChild(deadline);
    }


    // Generating button to bigview project
    let button = document.createElement("button");
    button.classList.add("btn", "btn-primary");
    button.innerHTML = "Megtekintés";
    button.onclick = function () {
        window.location.href = "bigview.php?projectID=" + project.ID;
    }
    body.appendChild(button);

}

// Function to generate a trello like project

async function generateProjectBody(project) {
    //console.log("Generating project body");
    //console.log(project);
    let projectName = project.Name;
    let projectID = project.ID;

    // Div to add projects to
    const projectHolder = document.getElementById("projectHolder");

    // Create a new project holder card
    let projectCard = document.createElement("div");
    projectCard.classList.add("card", "projectCard");
    projectCard.id = projectID;
    colorCardBasedOnDeadline(projectCard, project.Deadline);
    //projectCard.draggable = true; // Make the projectCard draggable
    projectHolder.appendChild(projectCard);

    // Create a new project header
    let projectHeader = document.createElement("div");
    projectHeader.classList.add("card-header", "projectTitle");

    // Create nav
    let nav = document.createElement("ul");
    nav.classList.add("nav", "nav-tabs", "card-header-tabs");

    // Create li elements
    let tasks = document.createElement("li");
    tasks.classList.add("nav-item");
    //tasks.innerHTML = `<button class="nav-link active" id="task-tab" data-bs-toggle="tab" data-bs-target="#task-tab-pane-${projectID}" type="button" role='tab' aria-controls='task-tab-pane' aria-selected='true'>
    //<a data-bs-toggle="tooltip" data-bs-title="${project.Description}">${projectName}</a></button>`;
    tasks.innerHTML = `<button class="nav-link active" id="task-tab" data-bs-toggle="tab" data-bs-target="#task-tab-pane-${projectID}" type="button" role='tab' aria-controls='task-tab-pane' aria-selected='true'>${projectName}</button>`;
    nav.appendChild(tasks);

    let members = document.createElement("li");
    members.classList.add("nav-item");
    members.innerHTML = "<button class='nav-link' id='users-tab' data-bs-toggle='tab' data-bs-target='#users-tab-pane-" + projectID + "' type='button' role='tab' aria-controls='users-tab-pane' aria-selected='false'>Tagok</button>";
    nav.appendChild(members);

    projectHeader.appendChild(nav);

    let infoDiv = document.createElement("div");
    infoDiv.classList.add("infoDiv");

    if (project.Deadline) {
        var deadlineText = await getDeadline(project.Deadline);
        let deadline = document.createElement("span");
        deadline.classList.add("badge", "ms-2");
        deadline.innerHTML = `<a data-bs-toggle="tooltip" data-bs-title="${project.Deadline.slice(0, -3)}">${deadlineText}</a>`;
        let deadlineColor = getDeadlineColor(project.Deadline);
        switch (deadlineColor) {
            case "longAgo":
                deadline.classList.add("bg-secondary");
                break;
            case "overdue":
                deadline.classList.add("bg-danger");
                break;
            case "soon":
                deadline.classList.add("bg-warning", "text-dark");
                break;
            case "future":
                deadline.classList.add("bg-success", "text-white");
                break;
        }
        infoDiv.appendChild(deadline);
    }

    // Add settings button to project title
    try {
        infoDiv.appendChild(changeProjectSettingsButton(projectID));
    } catch (error) {
        ;
    }
    projectHeader.appendChild(infoDiv);

    projectCard.appendChild(projectHeader);

    // Create a card project body
    let CardBody = document.createElement("div");
    CardBody.classList.add("card-body", "tab-content");
    projectCard.appendChild(CardBody);


    // Create a new project body
    let projectBody = document.createElement("div");
    projectBody.classList.add("projectBody", "tab-pane", "fade", "show", "active");
    projectBody.id = "task-tab-pane-" + projectID;
    projectBody.role = "tabpanel";
    projectBody.ariaLabelledby = "task-tab";
    CardBody.appendChild(projectBody);


    // Create a new project description
    projectBody.appendChild(createDiscription(projectID, project.Description));

    // Generating the project tasks
    projectBody.appendChild(await generateTasks(projectID, project.canEdit));

    if (project.canEdit) {
        let addTask = document.createElement("button");
        addTask.classList.add("btn", "btn-success", "dropdown-toggle", "addTask");
        addTask.innerHTML = "Új hozzáadása";
        addTask.setAttribute("data-bs-toggle", "dropdown");
        projectBody.appendChild(addTask);

        // Creat ul dropdown
        let ul = document.createElement("ul");
        ul.classList.add("dropdown-menu");
        projectBody.appendChild(ul);

        // create li elements
        let task = document.createElement("li");
        task.classList.add("dropdown-item");
        task.innerHTML = "<i class='fas fa-stream fa-sm'></i> Feladat";
        task.style.cursor = "pointer";
        task.onclick = function () {
            addNewTask(projectID, "task", project.Deadline);
        }
        ul.appendChild(task);

        let checklist = document.createElement("li");
        checklist.classList.add("dropdown-item");
        checklist.innerHTML = "<i class='fas fa-list fa-sm'></i> Lista";
        checklist.style.cursor = "pointer";
        checklist.onclick = function () {
            addNewTask(projectID, "checklist", project.Deadline);
        }
        ul.appendChild(checklist);

        let radio = document.createElement("li");
        radio.classList.add("dropdown-item");
        radio.innerHTML = "<i class='fas fa-dot-circle fa-sm'></i> Választós lista";
        radio.style.cursor = "pointer";
        radio.onclick = function () {
            addNewTask(projectID, "radio", project.Deadline);
        }
        ul.appendChild(radio);
    }
    // Create Body for members
    let membersBody = document.createElement("div");
    membersBody.classList.add("projectMembers", "tab-pane", "fade");
    membersBody.id = "users-tab-pane-" + projectID;
    membersBody.role = "tabpanel";
    membersBody.ariaLabelledby = "users-tab";
    CardBody.appendChild(membersBody);


    try {
        // Add + button to add new members
        membersBody.appendChild(await editProjectMembersButton(projectID));
    } catch (error) {
        ;
    }
    // Adding members to the project body
    membersBody.appendChild(await generateMembers(projectID));




    return projectCard;
}



// Extra


function colorCardBasedOnDeadline(projectCard, deadline) {
    if (deadline) {
        let now = new Date();
        let projectDeadline = new Date(deadline);

        if (projectDeadline < now) {
            projectCard.classList.add("overdue");
            return "overdue";
        } else if (projectDeadline - now < (1000 * 60 * 60 * 48)) {   // 48 hours
            projectCard.classList.add("soon");
            return "soon";
        } else {
            projectCard.classList.add("future");
            return "future";
        }
    }
}

function getDeadlineColor(deadline) {
    if (deadline) {
        let now = new Date();
        let projectDeadline = new Date(deadline);

        // If task is overdue more than a week, return
        if (now - projectDeadline > (1000 * 60 * 60 * 24 * 7)) {
            return "longAgo";
        } else if (projectDeadline < now) {
            return "overdue";
        } else if (projectDeadline - now < (1000 * 60 * 60 * 48)) {   // 48 hours
            return "soon";
        } else {
            return "future";
        }
    }
}

function createDiscription(projectID, Description) {
    // Create a new project description
    const projectDescriptionHolder = document.createElement("div");
    projectDescriptionHolder.classList.add("projectDescriptionHolder");


    // Make the description max 100 characters
    if (Description.length > 100) {
        var ShortDescription = `${makeFormatting(Description.slice(0, 60)) || 'Nincs leírás'} <a class="descToggle" onclick=ToggleDesc(${projectID})>Több...</a>`;
    }

    const projectDescription = document.createElement("span");
    projectDescription.classList.add("card-text", "projectDescription");
    projectDescription.innerHTML = ShortDescription || Description;
    projectDescription.setAttribute("fullDescription", Description);
    projectDescriptionHolder.appendChild(projectDescription);

    return projectDescriptionHolder;
}

function ToggleDesc(projectID) {
    const desc = document.getElementById(projectID).getElementsByClassName("projectDescription")[0];
    const descToggle = desc.getElementsByClassName("descToggle")[0];
    const Description = desc.getAttribute("fullDescription");

    if (descToggle.innerHTML == "Több...") {
        desc.innerHTML = `${makeFormatting(Description)} <a class="descToggle" onclick=ToggleDesc(${projectID})>Kevesebb...</a>`;
    } else {
        desc.innerHTML = `${makeFormatting(Description.slice(0, 60))} <a class="descToggle" onclick=ToggleDesc(${projectID})>Több...</a>`;
    }
}


async function generateMembers(proj_id) {
    let membersHolder = document.createElement("div");
    membersHolder.classList.add("membersHolder");

    // Load project members
    fetchProjectMembers(proj_id)
        .then(async response => {
            projectMembers = JSON.parse(response);

            // Append each member to membersHolder
            projectMembers.forEach(async element => {
                membersHolder.appendChild(await createMember(element, proj_id));
            });
        })
        .catch(error => {
            console.error('Error fetching project members:', error);
            //serverErrorToast();
        });

    return membersHolder;
}

async function createMember(member, projectID) {
    let memberCard = document.createElement("div");
    memberCard.classList.add("card", "memberCard", "mb-2");
    if (member.isManager) {
        memberCard.classList.add("manager");
    }

    let memberBody = document.createElement("div");
    memberBody.classList.add("card-body", "memberBody");

    let memberName = document.createElement("span");
    memberName.classList.add("card-text", "memberName");
    memberName.innerHTML = member.lastName + " " + member.firstName;
    memberBody.appendChild(memberName);

    try {
        if (!member.isManager) {
            memberBody.appendChild(removeMemberFromProjectButton(projectID, member.UserID));
        }
        memberCard.oncontextmenu = async function (e) {
            if (this.classList.contains("manager")) {
                return;
            }
            e.preventDefault();
            if (await changeManager(projectID, member.UserID) == 200) {
                this.classList.add("manager");
                this.getElementsByClassName("memberBody")[0].querySelector(".removeMemberButton").remove();
            }
        }

        let touchCount = 0;
        //MOBILE DOUBLE TAP
        memberCard.addEventListener('touchend', function (event) {
            const memberCard = this;
            touchCount++;
            if (touchCount === 1) {
                setTimeout(async function () {
                    if (touchCount === 2) {
                        if (memberCard.classList.contains("manager")) {
                            return;
                        }
                        navigator.vibrate(100);
                        if (await changeManager(projectID, member.UserID) == 200) {
                            memberCard.classList.add("manager");
                            memberCard.getElementsByClassName("memberBody")[0].querySelector(".removeMemberButton").remove();
                        }
                    }
                    touchCount = 0;
                }, 300); // 300 milliseconds = 0.3 seconds
            }
        });
    } catch (error) {
        ;
    }

    memberCard.appendChild(memberBody);

    return memberCard;
}



function getDeadline(deadline) {
    // If there is no deadline
    if (!deadline) {
        return "";
    }
    // Get the deadline

    let now = new Date();
    let projectDeadline = new Date(deadline);

    let diff = projectDeadline - now;
    var differenceInSeconds = Math.floor(diff / 1000);
    var differenceInMinutes = Math.floor(differenceInSeconds / 60);
    var differenceInHours = Math.floor(differenceInMinutes / 60);
    var differenceInDays = Math.floor(differenceInHours / 24);

    if (differenceInDays > 0) return differenceInDays + " nap";
    if (differenceInHours > 0) return differenceInHours + " óra";
    if (differenceInMinutes > 0) return differenceInMinutes + " perc";
    if (differenceInSeconds >= 0) return "Épp most";

    // if the deadline is overdue more than a week
    if (differenceInDays < -7) return "Régen";
    return "Lejárt";
}