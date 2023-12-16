document.addEventListener('DOMContentLoaded', function() {
  const video = document.getElementById('introvid');
  const playPauseBtn = document.getElementById('play-pause-btn');
  const progressBar = document.getElementById('progress-bar');
  const fullscreenBtn = document.getElementById('fullscreen-btn');

  playPauseBtn.addEventListener('click', function() {
    if (video.paused || video.ended) {
      video.play();
      playPauseBtn.innerHTML = '<img src="pause-icon.pff" alt="Pause">';/* TBM */
    } else {
      video.pause();
      playPauseBtn.innerHTML = '<img src="play-icon.pff" alt="Play">';/* TBM */
    }
  });
  video.addEventListener('ended', function() {
    playPauseBtn.innerHTML = '<img src="play-icon.pff" alt="Play">';/* TBM */
  });

  video.addEventListener('timeupdate', function() {
    progressBar.value = (video.currentTime / video.duration) * 100;
  });
  progressBar.addEventListener('input', function() {
    video.currentTime = (progressBar.value / 100) * video.duration;
  });

  fullscreenBtn.addEventListener("click", function() {
    if (video.requestFullscreen) {
      video.requestFullscreen();
    } else if (video.mozRequestFullScreen) {
      video.mozRequestFullScreen();
    } else if (video.webkitRequestFullscreen) {
      video.webkitRequestFullscreen();
    }
  });
  document.addEventListener("fullscreenchange", () => video.controls = document.fullscreenElement ? true : false);
});
