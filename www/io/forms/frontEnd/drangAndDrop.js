
const position = { x: 0, y: 0 }

interact('.draggable').draggable({
    listeners: {
        start(event) {
            event.target.classList.add('dragging');
            event.preventDefault();
        },
        move(event) {
            position.x += event.dx
            position.y += event.dy

            event.target.style.transform =
                `translate(${position.x}px, ${position.y}px)`
        },
        end(event) {
            event.target.classList.remove('dragging');
            position.x = 0;
            position.y = 0;
            event.target.style.transform = 'none';
            updateOrder();
        },
    },
    allowFrom: '.dragHandle',
    startAxis: 'y',
    lockAxis: 'y',
    autoScroll: true,
})


interact('#editorZone')
    .dropzone({
        ondrop: function (event) {
            const editorZone = document.getElementById('editorZone');
            const droppedElement = event.relatedTarget; // The element being dropped
            droppedElement.style.transform = 'none'; // Reset the transform to keep the element in place within the editorZone

            // Use clientY for the Y-coordinate of the drop location
            const afterElement = getDragAfterElement(editorZone, event.dragEvent.clientY);
            console.log(afterElement);
            if (afterElement == null) {
                editorZone.appendChild(droppedElement);
            } else {
                editorZone.insertBefore(droppedElement, afterElement);
            }
        }
    })
    .on('dropactivate', function (event) {
        event.target.classList.add('drop-activated');
    });


function getDragAfterElement(container, y) {
    const draggableElements = [...container.querySelectorAll('.form-member:not(.dragging)')];

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


function updateOrder() {

    const formMembers = document.querySelectorAll('.form-member');
    const newOrderIds = [];

    formMembers.forEach((formMember, index) => {
        newOrderIds.push(+formMember.id.split('-')[1]);
    });

    // Filter formElements array based on the new order
    formElements.sort((a, b) => newOrderIds.indexOf(a.id) - newOrderIds.indexOf(b.id));

    // Save the new order to the database
    saveFormElements(false);
}