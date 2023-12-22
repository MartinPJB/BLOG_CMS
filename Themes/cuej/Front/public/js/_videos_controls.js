document.addEventListener('DOMContentLoaded', function() {
  const videos = document.getElementsByClassName('video-block__video');

  for (const video of videos) {
    const controls = video.parentElement.querySelector('.video-controls');
    const playPauseBtns = controls.getElementsByClassName('play-pause-btn');
    const progressBar = controls.querySelector('.vidprogress-bar');
    const muteBtn = controls.querySelector('.mute-btn');
    const volumeBar = controls.querySelector('.volume-bar');
    const fullscreenBtn = controls.querySelector('.fullscreen-btn');
    const timecode = controls.querySelector('.timecode');

    const formatTime = function(seconds) {
      const minutes = Math.floor(seconds / 60);
      const remainingSeconds = Math.floor(seconds % 60);
      return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
    };

    let controlsVisible = true;
    let hideControlsTimeout;

    const toggleControlsVisibility = function() {
      controlsVisible = !controlsVisible;
      controls.style.opacity = controlsVisible ? '1' : '0';
    };

    const startHideControlsTimer = function() {
      hideControlsTimeout = setTimeout(() => {
        if (controlsVisible) {
          toggleControlsVisibility();
        }
      }, 2000); // 2000 milliseconds (2 seconds) before hiding controls
    };

    const resetHideControlsTimer = function() {
      clearTimeout(hideControlsTimeout);
      startHideControlsTimer();
    };

    const handlePlayPause = function() {
      if (video.paused || video.ended) {
        video.play();
        playPauseBtns[0].style.display = "none";
        for (const btn of playPauseBtns) {
          btn.firstElementChild.alt = "Pause";
          btn.firstElementChild.src = "public/front/img/svg/icon-pause.svg";
        }
      } else {
        video.pause();
        playPauseBtns[0].style.display = "block";
        for (const btn of playPauseBtns) {
          btn.firstElementChild.alt = "Jouer";
          btn.firstElementChild.src = "public/front/img/svg/icon-play.svg";
        }
      }
      resetHideControlsTimer();
    };

    for (const playPauseBtn of playPauseBtns) {
      playPauseBtn.addEventListener('click', handlePlayPause);
    }
    video.addEventListener('click', handlePlayPause);

    video.addEventListener('ended', function() {
      playPauseBtns[0].style.display = "block";
      for (const btn of playPauseBtns) {
        btn.firstElementChild.alt = "Jouer";
        btn.firstElementChild.src = "public/front/img/svg/icon-play.svg";
      }
      resetHideControlsTimer();
    });

    video.addEventListener('timeupdate', function() {
      progressBar.value = (video.currentTime / video.duration) * 100;
      timecode.textContent = `${formatTime(video.currentTime)} / ${formatTime(video.duration)}`;
      resetHideControlsTimer();
    });

    progressBar.addEventListener('input', function() {
      video.currentTime = (progressBar.value / 100) * video.duration;
      resetHideControlsTimer();
    });

    muteBtn.addEventListener('click', function() {
      video.muted = !video.muted;
      muteBtn.alt = video.muted ? "Volume désactivé" : "Volume activé";
      resetHideControlsTimer();
    });

    volumeBar.addEventListener('input', function() {
      video.volume = volumeBar.value / 100;
      resetHideControlsTimer();
    });

    fullscreenBtn.addEventListener('click', function() {
      if (video.requestFullscreen) {
        video.requestFullscreen();
      } else if (video.mozRequestFullScreen) {
        video.mozRequestFullScreen();
      } else if (video.webkitRequestFullscreen) {
        video.webkitRequestFullscreen();
      }
      resetHideControlsTimer();
    });

    document.addEventListener('fullscreenchange', () => {
      video.controls = document.fullscreenElement ? true : false;
      resetHideControlsTimer();
    });

    video.addEventListener('mousemove', function() {
      if (!controlsVisible) {
        toggleControlsVisibility();
        resetHideControlsTimer();
      }
    });

    video.addEventListener('mouseout', function(event) {
      if (!event.relatedTarget && controlsVisible) {
        toggleControlsVisibility();
      }
    });
  }
});
