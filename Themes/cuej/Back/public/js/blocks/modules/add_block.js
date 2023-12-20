/**
 * Library for handling block creation.
 * @namespace
*/
const block_create = {
  /**
   * Handles the show form function.
   * @function
   * @param {Event} e - The event triggering the displayForm function.
  */
  displayForm(e) {
    const { articleid: articleID, type: targetForm } = e.target.dataset;
    const form = window.fields[targetForm];

    if (!form || !articleID) {
      return;
    }

    const formContainer = document.querySelector("#cuej__block-creation-container");
    formContainer.innerHTML = ""; // Clear previous content
    formContainer.appendChild(this.createFormElement(articleID, targetForm, form, e.target.dataset.blockid));

    if (e.target.dataset.json) {
      this.fillForm(formContainer, JSON.parse(e.target.dataset.json), e.target.dataset.name);
    };

    document.querySelector("#step__1").classList.toggle("hidden");
    document.querySelector("#step__2").classList.toggle("hidden");
  },

  /**
   * Creates and returns the form element.
   * @function
   * @param {string} articleID - The ID of the article.
   * @param {string} targetForm - The target form.
   * @param {Object} form - The form data.
   * @param {int} blockId - The block id
   * @returns {HTMLFormElement} - The created form element.
  */
  createFormElement(articleID, targetForm, form, blockId) {
    const formHTML = document.createElement("form");
    const mode = blockId ? "edit" : "create";
    formHTML.id = "cuej__block-creation-form";
    formHTML.action = `admin/${mode}_block/${articleID}`;
    formHTML.method = "POST";

    // Add hidden input for type
    formHTML.appendChild(this.createHiddenInput("type", targetForm));

    // Add name input
    formHTML.appendChild(this.createInputLabelAndInput("Block name", "name", "text", "Block name"));

    // Add other form inputs
    for (const input in form) {
      formHTML.appendChild(this.createInputLabelAndInput(form[input].label, input, form[input].type, form[input].label, form[input].min, form[input].max));
    }

    // Add submit button
    formHTML.appendChild(this.createSubmitButton(blockId));

    if (blockId) {
      console.log(blockId);
      formHTML.appendChild(this.createHiddenInput('blockId', blockId));
    }

    return formHTML;
  },

  /**
   * Creates and returns a hidden input element.
   * @function
   * @param {string} name - The name attribute of the input.
   * @param {string} value - The value attribute of the input.
   * @returns {HTMLInputElement} - The created hidden input element.
  */
  createHiddenInput(name, value) {
    const hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = name;
    hiddenInput.value = value;
    return hiddenInput;
  },

  /**
   * Creates and returns a label and input element.
   * @function
   * @param {string} labelText - The text content of the label.
   * @param {string} inputID - The ID attribute of the input.
   * @param {string} inputType - The type attribute of the input.
   * @param {string} inputPlaceholder - The placeholder attribute of the input.
   * @param {number} [min] - The min attribute of the input (optional).
   * @param {number} [max] - The max attribute of the input (optional).
   * @returns {DocumentFragment} - The created label and input elements.
  */
  createInputLabelAndInput(labelText, inputID, inputType, inputPlaceholder, min, max) {
    const fragment = document.createDocumentFragment();

    // Create section
    const section = document.createElement("section");

    // Create label
    const label = document.createElement("label");
    label.htmlFor = inputID;
    label.innerHTML = labelText;

    // Create input
    const input = inputType === "textarea" ? document.createElement("textarea") : document.createElement("input");

    // Set input type for non-textarea elements
    if (inputType !== "textarea") {
      input.type = inputType;
    }

    input.name = inputID;
    input.id = inputID;
    input.placeholder = inputPlaceholder;

    // Add min and max attributes if present
    if (min !== undefined) {
      input.min = min;
    }

    if (max !== undefined) {
      input.max = max;
    }

    // Append label and input to fragment
    section.appendChild(label);
    section.appendChild(input);
    fragment.appendChild(section);

    return fragment;
  },

  /**
   * Creates and returns a submit button element.
   * @function
   * @returns {HTMLButtonElement} - The created submit button element.
   * @param {int} blockId - The block id
  */
  createSubmitButton(blockId) {
    const submitButton = document.createElement("button");
    submitButton.type = "submit";
    submitButton.innerHTML = (blockId ? "Update" : "Create") + " block";
    return submitButton;
  },

  /**
   * Fills the form with existing values.
   * @function
   * @param {form} - The form container.
   * @param {values} - The values to update.
   * @param {name} - The block name.
  */
  fillForm(form, values, name) {
    form.querySelector('[name=name]').value = name;
    for (const fieldName in values) {
      form.querySelector(`[name="${fieldName}"]`).value = values[fieldName];
    }

  },

  /**
   * Handles the hide form function.
   * @function
   * @param {Event} e - The event triggering the hideForm function.
  */
  hideForm(e) {
    document.querySelector("#step__1").classList.toggle("hidden");
    document.querySelector("#step__2").classList.toggle("hidden");
    document.querySelector("#cuej__block-creation-container").innerHTML = "";
  },

  /**
   * Assigns the create block function to the create button.
   * @function
  */
  assignButtons() {
    const blockButtons = document.querySelectorAll(".cuej__block-creation");
    for (const button of blockButtons) {
      button.addEventListener("click", this.displayForm.bind(this));
    }

    const updateButtons = document.querySelectorAll(".cuej__block-update");
    for (const button of updateButtons) {
      button.addEventListener("click", this.displayForm.bind(this));
    }


    const backbutton = document.querySelector("#cuej__block-creation-back");
    backbutton.addEventListener("click", this.hideForm.bind(this));
  }
};

export default block_create;
