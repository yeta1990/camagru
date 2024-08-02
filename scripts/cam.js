(() => {
  
    const width = 800; 
    let height = 0; 
  
    let streaming = false;
  
    let video = null;
    let canvas = null;
    let startbutton = null;
    let takeAnotherButton = null;
    let publishButton = null;
  
    function startup() {

      video = document.getElementById("video");
      canvas = document.getElementById("canvas");
      startbutton = document.getElementById("startbutton");
      takeAnotherButton = document.getElementById("takeanother");
      publish = document.getElementById("publish");
      takeAnotherButton.style.display = "none";
      publish.style.display = "none";
  
      navigator.mediaDevices
        .getUserMedia({ video: true, audio: false })
        .then((stream) => {
          video.srcObject = stream;
          video.play();
        })
        .catch((err) => {
          console.error(`An error occurred: ${err}`);
        });
  
      video.addEventListener(
        "canplay",
        (ev) => {
          if (!streaming) {
            height = video.videoHeight / (video.videoWidth / width);
  
            if (isNaN(height)) {
              height = width / (4 / 3);
            }
  
            video.setAttribute("width", width);
            video.setAttribute("height", height);
            canvas.setAttribute("width", width);
            canvas.setAttribute("height", height);
            streaming = true;
          }
        },
        false,
      );
  
      startbutton.addEventListener(
        "click",
        (ev) => {
          takepicture();
          ev.preventDefault();
        },
        false,
      );

      takeAnotherButton.addEventListener(
        "click",
        (ev) => {
          takeAnother();
          ev.preventDefault();
        },
        false,
      );
  
    }
  
  
    function clearphoto() {
      const context = canvas.getContext("2d");
      context.fillStyle = "#AAA";
      context.fillRect(0, 0, canvas.width, canvas.height);
  
      const data = canvas.toDataURL("image/png");
      //photo.setAttribute("src", data);
    }
  
    function takepicture() {
      const context = canvas.getContext("2d");
      if (width && height) {
        canvas.width = width;
        canvas.height = height;
        context.drawImage(video, 0, 0, width, height);
  
        const data = canvas.toDataURL("image/png");
        console.log("take foto");
        //cam.setAttribute("src", data);
        document.getElementById("canvas").style.display = "block";
        document.getElementById("video").style.display = "none";
        
        startbutton.style.display = "none";
        takeAnotherButton.style.display = "block";
        document.getElementById("publish").style.display = "block";
      } else {
        clearphoto();
      }
    }
  

    function takeAnother() {
        document.getElementById("video").style.display = "block";
        document.getElementById("canvas").style.display = "none";
        document.getElementById("startbutton").style.display = "block";
        document.getElementById("takeanother").style.display = "none";
        document.getElementById("publish").style.display = "none";
    }

    window.addEventListener("load", startup, false);
  })();