document.addEventListener('DOMContentLoaded', function() {
  const video = document.getElementById('introvid');
  const landfg = document.querySelector(".land__foreground");
  const playPauseBtn = document.getElementById('play-pause-btn');
  const progressBar = document.getElementById('progress-bar');
  const fullscreenBtn = document.getElementById('fullscreen-btn');
  let neverStarted = true;

  function playPause() {
    if (neverStarted) {
      landfg.parentElement.style.height = "100vh";
      landfg.parentElement.style.marginBottom = "10vw";
      neverStarted = false;
    }
    if (video.paused || video.endend) {
      playPauseBtn.classList.add("land__foreground--blink");
      video.play();
      video.oncanplaythrough = () => {
        playPauseBtn.classList.remove("land__foreground--blink");
        landfg.classList.add("land__foreground--videoplay");
        playPauseBtn.classList.add("played");
        playPauseBtn.classList.remove("paused");
      };
      video.onplay = () => {
        playPauseBtn.classList.remove("land__foreground--blink");
        landfg.classList.add("land__foreground--videoplay");
        playPauseBtn.classList.add("played");
        playPauseBtn.classList.remove("paused");
      };
    } else {
      video.pause();
      playPauseBtn.classList.remove("land__foreground--blink");
      landfg.classList.remove("land__foreground--videoplay");
      playPauseBtn.classList.remove("played");
      playPauseBtn.classList.add("paused");
    }
  }

  playPauseBtn.addEventListener('click', function() {
    playPause();
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
  video.addEventListener('ended', () => {
    video.pause();
    playPauseBtn.classList.remove("land__foreground--blink");
    landfg.classList.remove("land__foreground--videoplay");
    playPauseBtn.classList.remove("played");
    playPauseBtn.classList.add("paused");
    document.getElementById('chapitres').scrollIntoView({ behavior: "smooth"});
  });
  document.addEventListener("fullscreenchange", () => video.controls = document.fullscreenElement ? true : false);
});
