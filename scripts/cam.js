    let width = 0; 
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
      if (document.getElementById("video").style.display == 'block'){
        
        height = video.videoHeight / (video.videoWidth / width);
        
        if (isNaN(height)) {
          height = width / (4 / 3);
        }
  
        video.setAttribute("width", width);
        video.setAttribute("height", height);
        canvas.setAttribute("width", width);
        canvas.setAttribute("height", height);
        document.getElementById("camContainer").style.height = height + 'px';
        document.getElementById("camContainer").style.width = width + 'px';
        streaming = true;
      }
    }


    function getInitialCamWidth(){
      if (window.innerWidth > 500 && navigator.userAgentData?.mobile){
        return 360;
      }
      else if (window.innerWidth > 500){
        return 500
      }
      return window.innerWidth;
    }

    function openCam(){
      document.getElementById("formFeedback").style.visibility= "hidden";
      width = getInitialCamWidth();
      video = document.getElementById("video");
      camContainer = document.getElementById("camContainer");
      camContainer.style.width = width;
      canvas = document.getElementById("canvas");
      startbutton = document.getElementById("startbutton");
      takeAnotherButton = document.getElementById("takeanother");
      publish = document.getElementById("publish");
      document.getElementById("takeanother-or").style.display = "none";
      takeAnotherButton.style.display = "none";
      publish.style.display = "none";
  
      navigator.mediaDevices
        .getUserMedia({ video: true, audio: false })
        .then((stream) => {
          video.srcObject = stream;
          video.play();
          
          document.getElementById("takePhotoContainer").style.display = "flex";
        })
        .catch((err) => {
          document.getElementById("formFeedback").textContent = "Unable to access camera. Check permissions or plug a working camera";
          document.getElementById("formFeedback").style.visibility= "visible";
          document.getElementById("takePhotoContainer").style.display = "none";
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
  
    }
  
    function takepicture() {
      
      const context = canvas.getContext("2d");
      if (width && height) {
        canvas.width = width;
        canvas.height = height;
        context.drawImage(video, 0, 0, width, height);
  
        data = canvas.toDataURL("image/png");
        document.getElementById("canvas").style.display = "block";
        document.getElementById("video").style.display = "none";
        
        startbutton.style.display = "none";
        takeAnotherButton.style.display = "block";
        document.getElementById("takeanother-or").style.display = "flex";
        document.getElementById("caption-cam-span").style.display = "block";
        document.getElementById("publish").style.display = "block";

        document.getElementById("camContainer").style.height = height + 'px';
        document.getElementById("camContainer").style.width = width + 'px';

      } else {
        clearphoto();
      }
    }
  

    function takeAnother() {
        document.getElementById("video").style.display = "block";
        document.getElementById("canvas").style.display = "none";
        document.getElementById("startbutton").style.display = "block";
        document.getElementById("takeanother").style.display = "none";
        document.getElementById("takeanother-or").style.display = "none";
        document.getElementById("publish").style.display = "none";
        document.getElementById("caption-cam-span").style.display = "none";
        document.getElementById("caption-cam").value = "";
    }

    function stopCam() {
      video = document.getElementById("video");
      if (video.width > 0){
        video.pause();
        video.src = "";
        video.srcObject.getTracks()[0].stop()
      }

    }

    document.getElementById("openCamera").addEventListener("click", () => {

      openCam();
      
      document.getElementById("publishMainContainer").style.display = "none";
      document.getElementById("formFeedback").style.visibility= "hidden";
      video.style = "";
      canvas.style.display = "none";
      takeAnother();
      
    })


    function uploadSuccessful() {
      stopCam();
      document.getElementById("publishMainContainer").style.display = "none";
      document.getElementById("takePhotoContainer").style.display = "none";
      document.getElementById("caption-cam").value = "";
    }


    document.getElementById("publishFromDevice").addEventListener("click", () => {

      stopCam();
      document.getElementById("publishMainContainer").style.display = "block";
      document.getElementById("takePhotoContainer").style.display = "none";
      document.getElementById("formFeedback").style.visibility= "hidden";
      document.getElementById("caption-cam").value = "";
      document.getElementById("imageFile").value = ''
      document.getElementById("imagePreview").style.display = "none";
      document.getElementById("formFeedback").style.color="white";
      document.getElementById("watermark-container").style.display = "none";
      
    })

    window.addEventListener('orientationchange', () => {
      updateCanvasSize();
    });

    document.getElementById('publish').addEventListener('click', function(event) {

        const token = localStorage.getItem('token');
        const headers = {};

        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }else if(!["/home", "/signup"].includes(window.location.pathname)){
            window.location.replace("/home");
        }
        event.preventDefault();

        fetch('/api/image/merge', {
            method: 'POST',
            headers,
            body: JSON.stringify(
              {
                "imageFile": data, 
                "caption": document.getElementById("caption-cam").value,
                "watermark": document.getElementById("watermark-display").src
              })
        })
        .then(response => {
             if (!response.ok) {
                 return response.json().then(err => {
                     throw new Error(err.message || "Upload failed");
                 });
             }   
             return response.json()
          
          })
        .then(data => {
            uploadSuccessful();
            document.getElementById("formFeedback").textContent = "Published successfully";
            document.getElementById("formFeedback").style.visibility= "visible";
            displayMyImages(data);
        })
        .catch(error => {
            uploadSuccessful();
            document.getElementById("formFeedback").textContent = error;
            document.getElementById("formFeedback").style.visibility = "visible";
        });
    });


    document.getElementById('cat').addEventListener("click", function(event){
         const src = document.getElementById("cat").firstElementChild.src
         document.getElementById("watermark-display").src = src;
     })
     document.getElementById('dog').addEventListener("click", function(event){
         const src = document.getElementById("dog").firstElementChild.src
         document.getElementById("watermark-display").src = src;
     })
     document.getElementById('sloth').addEventListener("click", function(event){
         const src = document.getElementById("sloth").firstElementChild.src
         document.getElementById("watermark-display").src = src;
     })

     document.getElementById('uploadForm').addEventListener('submit', function(event) {
         const token = localStorage.getItem('token');
         const headers = {};
         if (token) {
             headers['Authorization'] = `Bearer ${token}`;
         }else if(!["/home", "/signup"].includes(window.location.pathname)){
             window.location.replace("/home");
         }
         event.preventDefault();
         var formData = new FormData();
         var imageFile = document.getElementById('imageFile').files[0];
         formData.append('imageFile', imageFile);
         formData.append('width', document.getElementById("imagePreview").width);
         formData.append('height', document.getElementById("imagePreview").height);
         formData.append('caption', document.getElementById("caption").value);

         let watermarks = [];
         if (document.getElementById("watermarkPublisha").style.display == "block"){
          watermarks.push(document.getElementById("watermarkPublisha").firstElementChild.src);
         }
         if (document.getElementById("watermarkPublishb").style.display == "block"){
          watermarks.push(document.getElementById("watermarkPublishb").firstElementChild.src);
         }
         if (document.getElementById("watermarkPublishc").style.display == "block"){
          watermarks.push(document.getElementById("watermarkPublishc").firstElementChild.src);
         }

         formData.append('watermarks', watermarks);
         fetch('/api/image', {
             method: 'POST',
             headers,
             body: formData
         })
         .then(response => {
             if (!response.ok) {
                 return response.json().then(err => {
                     throw new Error(err.message || "Upload failed");
                 });
             }   
             return response.json()
         })
         .then(data => {
             uploadSuccessful();
             document.getElementById("formFeedback").textContent = "Published successfully";
             document.getElementById("formFeedback").style.visibility= "visible";
             displayMyImages(data);
         })
         .catch(error => {
          
             document.getElementById("formFeedback").textContent = error;
             document.getElementById("formFeedback").style.visibility = "visible";
         });
     });

     function toggleVisibleDiv(id){
        const div = document.getElementById(id);
        if (div.style.display == 'none'){
          div.style.display = 'block';
        }
        else{
          div.style.display = 'none';
        }
     }
     

    document.getElementById('cat2').addEventListener("click", function(event){
         const src = document.getElementById("cat2").firstElementChild.src
         console.log(src);
         document.getElementById("watermark-displayb").src = "assets/cat-big.png";
         toggleVisibleDiv('watermarkPublishb');
     })
     document.getElementById('dog2').addEventListener("click", function(event){
         const src = document.getElementById("dog2").firstElementChild.src
         document.getElementById("watermark-displaya").src = "assets/dog-big.png";
         toggleVisibleDiv('watermarkPublisha');
     })
     document.getElementById('sloth2').addEventListener("click", function(event){
         const src = document.getElementById("sloth2").firstElementChild.src
         document.getElementById("watermark-displayc").src = "assets/sloth-big.png";
         toggleVisibleDiv('watermarkPublishc');
     })


     document.getElementById('imageFile').addEventListener('change', function(event) {
      const file = event.target.files[0];
      const formFeedback = document.getElementById('formFeedback');
      const imagePreview = document.getElementById('imagePreview');
      document.getElementById("watermarkPublisha").style.display = 'none';
      document.getElementById("watermarkPublishb").style.display = 'none';
      document.getElementById("watermarkPublishc").style.display = 'none';
      
      formFeedback.style.visibility = 'hidden';
      imagePreview.style.display = 'none';
      document.getElementById("formFeedback").style.color = "white";
  
      if (file) {
          if (!validateMimeType(file)) {
              showError(formFeedback, 'Invalid image type. Only JPEG and PNG are allowed.');
              return;
          }
  
          const reader = new FileReader();
          reader.onload = function(e) {
              if (!validateMagicNumbers(e.target.result)) {
                  showError(formFeedback, 'Invalid image, file does not match any allowed MIME type.');
                  return;
              }
  
              validateImageDimensions(file, imagePreview, formFeedback);
          };
  
          reader.readAsArrayBuffer(file);
      }
  });
  
  function validateMimeType(file) {
      const validMimeTypes = ['image/jpeg', 'image/png'];
      return validMimeTypes.includes(file.type);
  }
  
  function validateMagicNumbers(buffer) {
      const arr = (new Uint8Array(buffer)).subarray(0, 4);
      let header = "";
      for (let i = 0; i < arr.length; i++) {
          header += arr[i].toString(16);
      }
  
      const magicNumbers = {
          '89504e47': 'image/png',
          'ffd8ffe0': 'image/jpeg',
          'ffd8ffe1': 'image/jpeg',
          'ffd8ffe2': 'image/jpeg',
      };
  
      return magicNumbers[header] !== undefined;
  }
  
  function validateImageDimensions(file, imagePreview, formFeedback) {
    // Validate the image's dimensions and aspect ratio for both horizontal and vertical orientations
    const imageUrl = URL.createObjectURL(file);
    const img = new Image();

    img.onload = function() {
        const minWidth = 800;
        const maxAspectRatio = 16 / 9;
        const imageAspectRatio = img.width >= img.height ? img.width / img.height : img.height / img.width;

        if (img.width < minWidth) {
            showError(formFeedback, `Image width must be at least ${minWidth}px.`);
        }
        else if (imageAspectRatio <= maxAspectRatio && imageAspectRatio >= 1) {
            imagePreview.src = imageUrl;
            imagePreview.style.display = 'block';
            document.getElementById("watermark-container").style.display = "flex";
        } else {
            showError(formFeedback, `Image must have a minimum aspect ratio of 1:1 and max of 16:9.`);
        }
    };

    img.src = imageUrl;
}
  
  function showError(formFeedback, message) {
      formFeedback.textContent = message;
      formFeedback.style.color = "red";
      formFeedback.style.visibility = 'visible';
      document.getElementById("watermark-container").style.display = "none";
  }