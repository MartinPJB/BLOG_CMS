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

export { swal_confirmAction, swal_success, swal_error };