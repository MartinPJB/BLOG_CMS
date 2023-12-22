import { waitForElm } from "../../modules/functions.js";

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
  async displayForm(e) {
    const { articleid: articleID, type: targetForm } = e.target.dataset;
    const form = window.fields[targetForm];

    if (!form || !articleID) {
      return;
    }

    const formContainer = document.querySelector("#cuej-block__creation-container");
    formContainer.innerHTML = ""; // Clear previous content
    formContainer.appendChild(this.createFormElement(articleID, targetForm, form, e.target.dataset.blockid));

    const event = new Event("show_choices");
    document.dispatchEvent(event);

    if (e.target.dataset.json) {
      const inputJSON = JSON.parse(e.target.dataset.json);
      if (inputJSON.media_id) {
        await waitForElm(".cuej-media__choose-existing");
      }

      this.fillForm(formContainer, inputJSON, e.target.dataset.name);
    };

    document.querySelector("#step-1").classList.toggle("hidden");
    document.querySelector("#step-2").classList.toggle("hidden");
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
    const mode_id = blockId ? blockId : articleID;
    formHTML.id = "cuej-block__creation-form";
    formHTML.action = `admin/${mode}_block/${mode_id}`;
    formHTML.method = "POST";
    formHTML.enctype = "multipart/form-data";

    // Add hidden input for type
    formHTML.appendChild(this.createHiddenInput("type", targetForm));

    // Add name input
    formHTML.appendChild(this.createInputLabelAndInput({
      label: "Block Name",
      inputID: "name",
      type: "text",
      inputPlaceholder: "Block Name",
      required: true
    }));

    // Add other form inputs
    for (const input in form) {
      form[input].inputID = input;
      formHTML.appendChild(this.createInputLabelAndInput(form[input]));
    }

    // Add submit button
    formHTML.appendChild(this.createSubmitButton(blockId));

    if (blockId) {
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
   * @param {object} inputField - The input data.
   * @returns {DocumentFragment} - The created label and input elements.
  */
  createInputLabelAndInput(inputField) {
    const fragment = document.createDocumentFragment();

    // Create section
    const section = document.createElement("section");

    // Create label
    const label = document.createElement("label");
    label.htmlFor = inputField.inputID;
    label.innerHTML = inputField.label;

    // Create input
    let input
    const inputType = inputField.type;
    switch (inputType) {
      case "textarea":
        input = document.createElement("textarea");
        break;
      case "select":
        input = document.createElement("select");
        const options = inputField.options.split(", ");
        for (const option of options) {
          const optionElement = document.createElement("option");
          optionElement.value = option;
          optionElement.innerHTML = option;
          input.appendChild(optionElement);
        }
        break;
      default:
        input = document.createElement("input");
        input.type = inputType;

        if (inputType === "file") {
          input.accept = inputField.accept;
          section.id = "cuej-media";

          const acceptWithoutDot = inputField.accept.replaceAll(".", "");
          section.dataset.type = acceptWithoutDot;
        }

        input.placeholder = inputField.label;
        break;
    }

    input.name = inputField.inputID;
    input.id = inputField.inputID;
    input.required = inputField.required ?? false;

    // Add min and max attributes if present
    if (inputField.min !== undefined) {
      input.min = inputField.min;
    }

    if (inputField.max !== undefined) {
      input.max = inputField.max;
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
      let input = form.querySelector(`[name="${fieldName}"]`);
      const value = values[fieldName];
      console.log(value);

      if (fieldName == "media_id") {
        input = document.querySelector(`#existing_media_${value}`);
        if (!input) continue;
        input.checked = true;
        continue;
      }
      if (!input) continue;

      form.querySelector(`[name="${fieldName}"]`).value = values[fieldName];
    }
  },

  /**
   * Handles the hide form function.
   * @function
   * @param {Event} e - The event triggering the hideForm function.
  */
  hideForm(e) {
    document.querySelector("#step-1").classList.toggle("hidden");
    document.querySelector("#step-2").classList.toggle("hidden");
    document.querySelector("#cuej-block__creation-container").innerHTML = "";
  },

  /**
   * Assigns the create block function to the create button.
   * @function
  */
  assignButtons() {
    const blockButtons = document.querySelectorAll(".cuej-block__creation");
    for (const button of blockButtons) {
      button.addEventListener("click", this.displayForm.bind(this));
    }

    const updateButtons = document.querySelectorAll(".cuej-block__update");
    for (const button of updateButtons) {
      console.log(button);
      button.addEventListener("click", this.displayForm.bind(this));
    }


    const backbutton = document.querySelector("#cuej-block__creation-back");
    backbutton.addEventListener("click", this.hideForm.bind(this));
  }
};

export default block_create;
