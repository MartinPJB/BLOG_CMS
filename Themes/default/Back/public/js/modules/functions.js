/**
 * Wait for a specified amount of time.
 * @param {number} ms - The time to wait in milliseconds.
 * @returns {Promise} - A promise that resolves when the time is elapsed.
 */
function wait(ms) {
  return new Promise((resolve) => setTimeout(resolve, ms));
}

/**
 * Wait for an event to be triggered on an item.
 * @param {HTMLElement} item - The item to listen to.
 * @param {string} event - The event to listen to.
 * @returns {Promise} - A promise that resolves when the event is triggered.
 */
function getPromiseFromEvent(item, event) {
  return new Promise((resolve) => {
    const listener = () => {
      item.removeEventListener(event, listener);
      resolve();
    };
    item.addEventListener(event, listener);
  });
}

/**
 * Wait for an element to be present in the DOM.
 * @param {string} selector - The selector to wait for.
 * @returns {Promise} - A promise that resolves to the element when it is found.
 */
function waitForElm(selector) {
  return new Promise((resolve) => {
    if (document.querySelector(selector)) {
      return resolve(document.querySelector(selector));
    }

    const observer = new MutationObserver((mutations) => {
      if (document.querySelector(selector)) {
        observer.disconnect();
        resolve(document.querySelector(selector));
      }
    });

    observer.observe(document.body, {
      childList: true,
      subtree: true,
    });
  });
}

export { wait, getPromiseFromEvent, waitForElm };
