/**
 * Library for handling block creation and dragging.
 * @namespace
 */
const drag_block = {
  /**
   * Handles the initialization of block dragging.
   * @function
   */
  initDrag() {
    const blockList = document.getElementById("blockDraggingUl");

    // Check if the block list exists
    if (!blockList) {
      console.error("Block list not found.");
      return;
    }

    // Initialize the block dragging
    this.makeListSortable(blockList);
  },

  /**
   * Makes the list sortable and handles the drag end event.
   * @function
   * @param {HTMLElement} list - The block list.
   */
  makeListSortable(list) {
    let draggingElement = null;

    // Function to handle drag start
    const handleDragStart = (e) => {
      draggingElement = e.target;
      e.dataTransfer.effectAllowed = "move";
      e.dataTransfer.setData("text/html", draggingElement.innerHTML);
      draggingElement.classList.add("dragging");
    };

    // Function to handle drag over
    const handleDragOver = (e) => {
      e.preventDefault();
      const targetElement = e.target;

      if (targetElement.tagName === "LI") {
        const boundingRect = targetElement.getBoundingClientRect();
        const offsetY = e.clientY - boundingRect.top;
        const isAfter = offsetY > boundingRect.height / 2;

        if (isAfter) {
          targetElement.classList.add("after-dragged");
        } else {
          targetElement.classList.remove("after-dragged");
        }
      }
    };

    // Function to handle drag enter
    const handleDragEnter = (e) => {
      e.preventDefault();
      const targetElement = e.target;

      if (targetElement.tagName === "LI") {
        targetElement.classList.add("over-dragged");
      }
    };

    // Function to handle drag leave
    const handleDragLeave = (e) => {
      const targetElement = e.target;

      if (targetElement.tagName === "LI") {
        targetElement.classList.remove("over-dragged", "after-dragged");
      }
    };

    // Function to handle drop
    const handleDrop = (e) => {
      e.preventDefault();
      const targetElement = e.target;


      if (targetElement.tagName === "LI") {
        const isAfter = targetElement.classList.contains("after-dragged");
        const droppedHTML = e.dataTransfer.getData("text/html");

        // Clone the original element
        const newListItem = draggingElement.cloneNode(true);
        newListItem.classList.remove("dragging", "over-dragged", "after-dragged");
        // const newListItem = document.createElement("li");
        // newListItem.draggable = true;
        // newListItem.innerHTML = droppedHTML;

        if (isAfter) {
          targetElement.parentNode.insertBefore(
            newListItem,
            targetElement.nextSibling
          );
        } else {
          targetElement.parentNode.insertBefore(newListItem, targetElement);
        }

        draggingElement.remove();
      }

      // Clean up drag-related styles
      targetElement.classList.remove("over-dragged", "after-dragged");
      draggingElement = null;
    };

    // Function to handle drag end
    const handleDragEnd = () => {
      // Clean up drag-related styles
      const listItems = document.querySelectorAll("li");
      listItems.forEach((item) =>
        item.classList.remove("over-dragged", "after-dragged")
      );
    };

    // Add drag-related event listeners to the list
    list.addEventListener("dragstart", handleDragStart);
    list.addEventListener("dragover", handleDragOver);
    list.addEventListener("dragenter", handleDragEnter);
    list.addEventListener("dragleave", handleDragLeave);
    list.addEventListener("drop", handleDrop);
    list.addEventListener("dragend", handleDragEnd);
  },
};

export default drag_block;
