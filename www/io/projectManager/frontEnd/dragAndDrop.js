
// Drag and Drop Functionality

function dragAndDropReady() {
    const taskHandles = document.querySelectorAll('.dragHandle');
    const taskHolders = document.querySelectorAll('.taskHolder');

    taskHandles.forEach(taskHandle => {
        taskHandle.addEventListener('dragstart', () => {
            //$('.toast').toast('hide');

            // Deselect everything on the page
            window.getSelection().removeAllRanges();

            let taskCard = taskHandle.parentElement.parentElement.parentElement;
            taskCard.classList.add('dragging');
            taskCard.draggable = true;
        });

        taskHandle.addEventListener('dragend', () => {
            let taskCard = taskHandle.parentElement.parentElement.parentElement;
            taskCard.classList.remove('dragging');
            taskCard.draggable = false;

            // Update task order
            updateTaksOrder(taskCard.parentElement);
        });
    });

    taskHolders.forEach(taskHolder => {
        taskHolder.addEventListener('dragover', (e) => {
            const dragging = document.querySelector('.dragging');
            // if element not part of taskHolder, return
            if (dragging.parentElement !== taskHolder) {
                return;
            }
            e.preventDefault();
            const afterElement = getDragAfterElement(taskHolder, e.clientY);
            if (afterElement == null) {
                if (taskHolder.lastElementChild !== dragging) {
                    taskHolder.appendChild(dragging);
                }
            } else {
                taskHolder.insertBefore(dragging, afterElement);
            }
        });
    });
}

function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.taskCard:not(.dragging)')];

    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;

        if (offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        } else {
            return closest;
        }
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}


async function updateTaksOrder(taskHolder) {
    const taskCards = taskHolder.querySelectorAll('.taskCard');
    let taskOrder = [];
    taskCards.forEach((taskCard, index) => {
        taskOrder.push({
            id: taskCard.id.split('-')[1],
            order: index
        });
    });

    // Update task order in database
    taskOrder = JSON.stringify(taskOrder);
    try {
        let response;

        response = await $.ajax({
            type: "POST",
            url: "../projectManager.php",
            data: { mode: "saveTaskOrder", tasks: taskOrder }
        });

        if (response == 200) {
            console.log("Task order saved successfully");
            simpleToast("Sorrend frissítve!");
        } else {
            errorToast("Hiba a sorrend mentésekor!");
        }

        //console.log(response);
    } catch (error) {
        serverErrorToast();
    }
}