function checkServerStatus() {
  fetch('JS/Alive.php')
    .then(res => {
      if (res.ok) {
        document.getElementById('server-warning').style.visibility = "hidden";
        document.body.classList.remove("dead_card_active");
        document.getElementById('modaldead').style.display = "none";
      } else {
        throw new Error();
      }
    })
    .catch(() => {
      document.getElementById('server-warning').style.visibility = "visible";
      document.body.classList.add("dead_card_active");
      document.getElementById('modaldead').style.display = "flex";
      // Stop All Playback sound!
      document.querySelectorAll('video, audio').forEach(media => {
      media.pause();
      media.currentTime = 0;
      });

      document.querySelectorAll('iframe').forEach(frame => {
          frame.src = 'about:blank';
      });
          });
      }

setInterval(checkServerStatus, 10000);