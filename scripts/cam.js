(() => {
  
    const width = 600; 
    let height = 0; 
  
    let streaming = false;
  
    let video = null;
    let canvas = null;
    let startbutton = null;
    let takeAnotherButton = null;
    let publishButton = null;
    let camContainer = null
    
    let data = null;
  
    function updateCanvasSize() {
      height = video.videoHeight / (video.videoWidth / width);
  
      if (isNaN(height)) {
        height = width / (4 / 3);
      }
  
      video.setAttribute("width", width);
      video.setAttribute("height", height);
      canvas.setAttribute("width", width);
      canvas.setAttribute("height", height);
      document.getElementById("camContainer").style.height = height + 'px';
      streaming = true;
    }

    function startup() {

      video = document.getElementById("video");
      camContainer = document.getElementById("camContainer");
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
              updateCanvasSize();
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
  
      //const data = canvas.toDataURL("image/png");
      //photo.setAttribute("src", data);
    }
  
    function takepicture() {
      const context = canvas.getContext("2d");
      if (width && height) {
        canvas.width = width;
        canvas.height = height;
        context.drawImage(video, 0, 0, width, height);
  
        data = canvas.toDataURL("image/png");
        console.log("take foto");
        //cam.setAttribute("src", data);
        document.getElementById("canvas").style.display = "block";
        document.getElementById("video").style.display = "none";
        
        startbutton.style.display = "none";
        takeAnotherButton.style.display = "block";
        document.getElementById("publish").style.display = "block";
        console.log(camContainer.style.height)

        document.getElementById("camContainer").style.height = height + 'px';
       // camContainer.setAttribute("width", width);
        //camContainer.setAttribute("height", height);
        //camContainer.style.height = height + 'px';
        //console.log(camContainer.style.height)
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

    window.addEventListener('orientationchange', () => {
      updateCanvasSize();
      console.log(window.innerWidth > window.innerHeight ? 'landscape' : 'portrait');
    });

    document.getElementById('video').addEventListener('seeked', (e) => {
        console.log(resize);
        console.log(e.target.videoWidth);
        console.log(e.target.videoHeight)
    });


    document.getElementById('publish').addEventListener('click', function(event) {

        const token = localStorage.getItem('token');
        const headers = {};

        if (token) {
            //to do: check exp date from token. if expired, remove token and redirect to home
            headers['Authorization'] = `Bearer ${token}`;
        }else if(!["/home", "/signup"].includes(window.location.pathname)){
            window.location.replace("/home");
        }
        event.preventDefault();
        /*
        var formData = new FormData();
        //var imageFile = document.getElementById('imageFile').files[0];
        formData.append('imageFile', data);
        formData.append('caption', "caption");
        formData.append('watermark', document.getElementById("watermark-display").src);
        //formData.append('caption', document.getElementById("caption").value);


        console.log(formData);
        */
        fetch('/api/image/merge', {
            method: 'POST',
            headers,
            body: JSON.stringify(
              {
                "imageFile": data, 
                "caption": caption, 
                "watermark": document.getElementById("watermark-display").src
              })
            //body: formData
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById("formFeedback").textContent = "Upload successfully";
            document.getElementById("formFeedback").style.visibility= "visible";
            console.log(data);
            displayMyImages(data);
        })
        .catch(error => {
          console.log(error);
            document.getElementById("formFeedback").textContent = error;
            document.getElementById("formFeedback").style.visibility = "visible";
        });
    });

  })();