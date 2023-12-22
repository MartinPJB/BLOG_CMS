import WaveSurfer from "./wavesurfer.esm.js";

const audios = document.querySelectorAll("audio");
const computedStyle = getComputedStyle(document.documentElement);

for (const audio of audios) {
  const audioPath = audio.src;
  const closestFigure = audio.closest(".audio-block__content");
  const button = closestFigure.parentElement.querySelector(".audio-block__control");

  const wavesurfer = WaveSurfer.create({
    container: closestFigure,
    waveColor: computedStyle.getPropertyValue("--color-primary"),
    progressColor: computedStyle.getPropertyValue("--color-primary--dark"),
    url: audioPath,
    cursorColor: computedStyle.getPropertyValue("--color-primary--darker"),
    cursorWidth: 2,
    barWidth: 7,
    barGap: 5,
    barRadius: 22,
    barHeight: 1,
    minPxPerSec: 1,
    fillParent: true,
    autoplay: false,
    interact: true,
    dragToSeek: true,
    hideScrollbar: false,
    audioRate: 1,
    autoScroll: true,
    autoCenter: true,
    sampleRate: 8000,
  });

  wavesurfer.on("click", () => wavesurfer.play());

  button.addEventListener("click", () => {
    button.classList.toggle("audio-block__control--play");
    button.classList.toggle("audio-block__control--pause");
    wavesurfer.playPause();
  });
}
