const pattern = ['c', 'e', 'd', 'r', 'i', 'c'];
let current = 0;

/**
 * Function to display Cedric on the page
 *
 * @returns
 */
function displayCedric() {
  const audioPartyBlower = new Audio('public/back/audio/partyblower.mp3');
  audioPartyBlower.play();

  const cedric = document.createElement('img');
  cedric.src = 'public/back/img/cedric.jpg';
  cedric.alt = 'Cedric!';
  cedric.id = 'cedric'

  cedric.style.top = Math.floor(Math.random() * 90) + '%';
  cedric.style.left = Math.floor(Math.random() * 90) + '%';

  document.body.appendChild(cedric);
}


/**
 * Triggered when a key is pressed on the keyboard. Detects if the key pressed is part of the pattern.
 *
 * @param {Event} event The event triggered by the key press
 * @returns
 */
function keyHandler(event) {
	if (pattern.indexOf(event.key) < 0 || event.key !== pattern[current]) {
		current = 0;
		return;
	}
	current++;

	if (pattern.length === current) {
		current = 0;

    // Create cedric
    displayCedric();
	}
};
document.addEventListener('keydown', keyHandler, false);