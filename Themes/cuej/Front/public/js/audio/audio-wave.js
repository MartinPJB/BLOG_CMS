import WaveSurfer from "./wavesurfer.esm.js";

let audiowave;

const audios = document.querySelectorAll("audio");
const computedStyle = getComputedStyle(document.documentElement);

for (const audio of audios) {
  const audioPath = audio.src;
  const closestFigure = audio.closest(".audio-block__content");

  let audiowave = {
    container: closestFigure,
    waveColor: computedStyle.getPropertyValue("--color-primary"),
    progressColor: computedStyle.getPropertyValue("--color-primary--dark"),
    url: audioPath,
    barRadius: 2,
    normalize: true,
    splitChannels: false,
    cursorColor: computedStyle.getPropertyValue("--color-primary--darker"),
    cursorWidth: 2,
    barWidth: 7,
    barGap: 5,
    barRadius: 22,
    barHeight: 1,
    minPxPerSec: 1,
    fillParent: true,
    mediaControls: true,
    autoplay: false,
    interact: true,
    dragToSeek: true,
    hideScrollbar: false,
    audioRate: 1,
    autoScroll: true,
    autoCenter: true,
    sampleRate: 8000,
  };

  const wavesurfer = WaveSurfer.create(audiowave);
  wavesurfer.on("click", function () {
    wavesurfer.play();
  });
}
