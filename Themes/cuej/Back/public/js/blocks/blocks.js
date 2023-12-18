// Functions
function displayForm(e) {
  const targetForm = e.target.dataset.type;
  const form = window.fields[targetForm];

  if (!form) {
    return;
  }

  document.querySelector("#step__1").classList.toggle("hidden");
  document.querySelector("#step__2").classList.toggle("hidden");

  const formContainer = document.querySelector("#cuej__block-creation-form");
  const formHTML = document.createElement("form");
  formHTML.id = "cuej__block-creation-form";
  formHTML.action = "/admin/create_block";
  formHTML.method = "POST";
  formContainer.appendChild(formHTML);

  for (const input in form) {
    const content = form[input];

    const label = document.createElement("label");
    label.htmlFor = input;
    label.innerHTML = content.label;

    const type = content.type;
    let htmlToGenerate;
    switch (type) {
      case "textarea":
        htmlToGenerate = document.createElement("textarea");
        break;

      default:
        htmlToGenerate = document.createElement("input");
        htmlToGenerate.type = type;
        break;
    }

    // Min and max
    if (content.min) {
      htmlToGenerate.min = content.min;
    }

    if (content.max) {
      htmlToGenerate.max = content.max;
    }

    htmlToGenerate.name = input;
    htmlToGenerate.id = input;

    formHTML.appendChild(label);
    formHTML.appendChild(htmlToGenerate);
    console.log(content);
  }

  const submitButton = document.createElement("button");
  submitButton.type = "submit";
  submitButton.innerHTML = "Create block";
  formHTML.appendChild(submitButton);
}

function hideForm(e) {
  document.querySelector("#step__1").classList.toggle("hidden");
  document.querySelector("#step__2").classList.toggle("hidden");
  document.querySelector("#cuej__block-creation-form").innerHTML = "";
}

function assignButtons() {
  const blockButtons = document.querySelectorAll(".cuej__block-creation");
  for (const button of blockButtons) {
    button.addEventListener("click", displayForm);
  }

  const backbutton = document.querySelector("#cuej__block-creation-back");
  backbutton.addEventListener("click", hideForm);
}
assignButtons();
