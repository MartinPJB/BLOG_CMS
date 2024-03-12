/**
 * This function is a wrapper around Swal.fire() that adds a confirm button for an action
 *
 * @param {object} swal_details The details to pass to Swal.fire()
 */
async function swal_confirmAction(swal_details) {

  const result = await Swal.fire({
    title: swal_details.title || "Are you sure?",
    text: swal_details.text || "This action cannot be undone.",
    icon: swal_details.icon || "warning",
    showCancelButton: swal_details.showCancelButton || true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: swal_details.confirmButtonText || "Yes, do it!",
  });

  return result.isConfirmed;
}

/**
 * This function is a wrapper around Swal.fire() that adds a success message
 *
 * @param {object} swal_details The details to pass to Swal.fire()
 */
async function swal_success(swal_details) {
  await Swal.fire({
    title: swal_details.title || "Success!",
    text: swal_details.text || "The action was performed successfully!",
    icon: swal_details.icon || "success",
  });
}

/**
 * This function is a wrapper around Swal.fire() that adds an error message
 *
 * @param {object} swal_details The details to pass to Swal.fire()
 */
async function swal_error(swal_details) {
  await Swal.fire({
    title: swal_details.title || "Error",
    text: swal_details.text || "An error occurred",
    icon: swal_details.icon || "error",
  });
}

/**
 * This function is a wrapper around Swal.fire() that adds a file input
 *
 * @param {object} swal_details The details to pass to Swal.fire()
 * @returns
 */
async function swal_file(swal_details) {
  const result = await Swal.fire({
    title: swal_details.title || "Select a file",
    input: "file",
    inputAttributes: {
      accept: "mp3, mp4, png, jpeg, jpg, webp, gif, svg",
      "aria-label": "Upload a new file",
    },
  });

  return result.value;
}

/**
 * This function is a wrapper around Swal.fire() that adds a text input and a textarea
 *
 * @param {object} swal_details The details to pass to Swal.fire()
 * @returns
 */
async function swal_input(swal_details) {
  const result = await Swal.fire({
    title: swal_details.title || "Enter a title",
    html:
      '<input id="swal-input1" class="swal2-input" placeholder="File\'s name">' +
      '<textarea id="swal-input2" class="swal2-textarea" placeholder="File\'s description"></textarea>',
    focusConfirm: false,
    preConfirm: () => {
      return [
        document.getElementById("swal-input1").value,
        document.getElementById("swal-input2").value,
      ];
    },
  });

  return result.value;
}

export { swal_confirmAction, swal_success, swal_file, swal_input, swal_error };