document.addEventListener('DOMContentLoaded', function() {
  const videoBlocks = document.querySelectorAll('.video-block');

  function togglePlayPause(video, playPauseBtns) {
    if (video.paused && !video.ended) {
      video.play();
      for (const playPauseBtn of playPauseBtns) {
        playPauseBtn.firstElementChild.src = "public/front/img/svg/icon-pause.svg";
        playPauseBtn.firstElementChild.alt = "Pause";
      }
    } else {
      video.pause();
      for (const playPauseBtn of playPauseBtns) {
        playPauseBtn.firstElementChild.src = "public/front/img/svg/icon-play.svg";
        playPauseBtn.firstElementChild.alt = "Jouer";
      }
      if (video.ended) {
        video.currentTime = 0;
      }
    }
  }

  function updateVolume(volumeBar, muteBtn, video) {
    if (video.muted || video.volume === 0) {
      volumeBar.value = 0;
      muteBtn.firstElementChild.src = "public/front/img/svg/icon-audio-mute.svg";
      muteBtn.firstElementChild.alt = "Volume désactivé";
    } else {
      volumeBar.value = video.volume * 100;
      muteBtn.firstElementChild.src = "public/front/img/svg/icon-audio.svg";
      muteBtn.firstElementChild.alt = "Volume activé";
    }
  }

  function formatTime(seconds) {
    const minutes = Math.floor(seconds / 60);
    const remainingSeconds = Math.floor(seconds % 60);
    return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
  }
  function updateTimecode(video, timecodeElement) {
    const currentTime = isNaN(video.currentTime) ? 0 : video.currentTime;
    const duration = isNaN(video.duration) ? 0 : video.duration;
    timecodeElement.textContent = `${formatTime(currentTime)} / ${formatTime(duration)}`;
  }

  function goFullscreen(video) {
    if (video.requestFullscreen) {
      video.requestFullscreen();
    } else if (video.mozRequestFullScreen) {
      video.mozRequestFullScreen();
    } else if (video.webkitRequestFullscreen) {
      video.webkitRequestFullscreen();
    }
  }

  function hideControls(controls) {
    controls.classList.add('video-controls--hidden');
  }
  function showControls(controls) {
    controls.classList.remove('video-controls--hidden');
  }


  for (const videoBlock of videoBlocks) {
    const video = videoBlock.querySelector('.video-block__video');
    const playPauseBtns = videoBlock.querySelectorAll('.play-pause-btn');
    const progressBar = videoBlock.querySelector('.vidprogress-bar');
    const timecodeElement = videoBlock.querySelector('.timecode');
    const volumeBar = videoBlock.querySelector('.volume-bar');
    const muteBtn = videoBlock.querySelector('.mute-btn');
    const fullscreenBtn = videoBlock.querySelector('.fullscreen-btn');

    for (const playPauseBtn of playPauseBtns) {
      playPauseBtn.addEventListener('click', () => {
        togglePlayPause(video, playPauseBtns);
      });
    }
    video.addEventListener('click', () => {
      if (!document.fullscreenElement) {
        togglePlayPause(video, playPauseBtns);
      }
    });
    video.addEventListener('ended', () => {
      togglePlayPause(video, playPauseBtns);
    });
    document.addEventListener('fullscreenchange', () => {
      togglePlayPause(video, playPauseBtns);
    });

    progressBar.addEventListener('input', () => {
      video.currentTime = (progressBar.value / 100) * video.duration;
    });
    video.addEventListener('timeupdate', () => {
      progressBar.value = (video.currentTime / video.duration) * 100;
      updateTimecode(video, timecodeElement);
    });
    video.addEventListener('loadedmetadata', () => {
      updateTimecode(video, timecodeElement);
    });

    volumeBar.addEventListener('input', () => {
      video.volume = volumeBar.value / 100;
      updateVolume(volumeBar, muteBtn, video);
    });
    video.addEventListener('volumechange', () => {
      updateVolume(volumeBar, muteBtn, video);
    });
    muteBtn.addEventListener('click', () => {
      video.muted = !video.muted;
      updateVolume(volumeBar, muteBtn, video);
    });

    fullscreenBtn.addEventListener('click', () => {
      goFullscreen(video);
    });
    video.addEventListener('dblclick', () => {
      goFullscreen(video);
    });

    const videoControls = videoBlock.querySelector('.video-controls');

    let isMouseMoving = false;
    let timeoutId;

    function toggleControls() {
      if (video.paused || video.ended) {
        showControls(videoControls);
        return;
      }
      if (!isMouseMoving) {
        hideControls(videoControls);
      }
      if (timeoutId) {
        clearTimeout(timeoutId);
      }
      timeoutId = setTimeout(() => {
        hideControls(videoControls);
      }, 2000);
    }
    video.addEventListener('mousemove', () => {
      isMouseMoving = true;
      showControls(videoControls);
      if (timeoutId) {
        clearTimeout(timeoutId);
      }
      timeoutId = setTimeout(() => {
        isMouseMoving = false;
        toggleControls();
      }, 2000);
    });
    video.addEventListener('mouseout', () => {
      isMouseMoving = false;
      toggleControls();
    });
    video.addEventListener('click', () => {
      toggleControls();
    });
    video.addEventListener('ended', () => {
      showControls(videoControls);
    });

  }
});
