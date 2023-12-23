document.addEventListener('DOMContentLoaded', function() {
  const video = document.getElementById('introvid');
  const landfg = document.querySelector(".land__foreground");
  const playPauseBtn = document.getElementById('play-pause-btn');
  const progressBar = document.getElementById('progress-bar');
  const fullscreenBtn = document.getElementById('fullscreen-btn');

  playPauseBtn.addEventListener('click', function() {
    if (landfg && !landfg.classList.contains("land__foreground--videoplay")) {
      landfg.classList.add("land__foreground--videoplay");
    }

    if (video.paused || video.ended) {
      video.play();
      playPauseBtn.innerHTML = '<img src="public/front/img/svg/icon-pause.svg" alt="Pause">';
    } else {
      video.pause();
      playPauseBtn.innerHTML = '<img src="public/front/img/svg/icon-play.svg" alt="Jouer">';
    }
  });
  video.addEventListener('ended', function() {
    playPauseBtn.innerHTML = '<img src="public/front/img/svg/icon-play.svg" alt="Jouer">';
  });


  if (progressBar) {
    video.addEventListener('timeupdate', function() {
      progressBar.value = (video.currentTime / video.duration) * 100;
    });

    progressBar.addEventListener('input', function() {
      video.currentTime = (progressBar.value / 100) * video.duration;
    });
  }

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
