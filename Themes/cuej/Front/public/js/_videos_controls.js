document.addEventListener('DOMContentLoaded', function() {
  const videos = document.getElementsByClassName('video-block__video');

  for (const video of videos) {
    const controls = video.parentElement.querySelector('.video-controls');
    const playPauseBtns = controls.getElementsByClassName('play-pause-btn');
    const progressBar = controls.querySelector('.vidprogress-bar');
    const muteBtn = controls.querySelector('.mute-btn');
    const volumeBar = controls.querySelector('.volume-bar');
    const fullscreenBtn = controls.querySelector('.fullscreen-btn');

    for (const playPauseBtn of playPauseBtns) {
      playPauseBtn.addEventListener('click', function() {
        if (video.paused || video.ended) {
          video.play();
          for (const btn of playPauseBtns) {
            btn.firstElementChild.alt = "Pause";
            btn.firstElementChild.src = "public/front/img/svg/icon-pause.svg";
          }
        } else {
          video.pause();
          for (const btn of playPauseBtns) {
            btn.firstElementChild.alt = "Jouer";
            btn.firstElementChild.src = "public/front/img/svg/icon-play.svg";
          }
        }
      });
    }

    video.addEventListener('ended', function() {
      for (const btn of playPauseBtns) {
        btn.firstElementChild.alt = "Jouer";
        btn.firstElementChild.src = "public/front/img/svg/icon-play.svg";
      }
    });

    video.addEventListener('timeupdate', function() {
      progressBar.value = (video.currentTime / video.duration) * 100;
    });

    progressBar.addEventListener('input', function() {
      video.currentTime = (progressBar.value / 100) * video.duration;
    });

    muteBtn.addEventListener('click', function() {
      video.muted = !video.muted;
      if (video.muted) {
        muteBtn.alt = "Volume désactivé";
      }else {
        muteBtn.alt = "Volume activé";
      }
    });

    volumeBar.addEventListener('input', function() {
      video.volume = volumeBar.value / 100;
    });

    fullscreenBtn.addEventListener('click', function() {
      if (video.requestFullscreen) {
        video.requestFullscreen();
      } else if (video.mozRequestFullScreen) {
        video.mozRequestFullScreen();
      } else if (video.webkitRequestFullscreen) {
        video.webkitRequestFullscreen();
      }
    });

    document.addEventListener('fullscreenchange', () => video.controls = document.fullscreenElement ? true : false);
  }
});
