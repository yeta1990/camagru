
async function fetchMyImages() {
    const images = await authFetch(`/api/image`)
        .then(data => {
            return data;
        })
        .catch(error => console.log(error)/*window.location.replace("/feed")*/);
    return images;
}

function setPublicationContent(publication, image){
    publication.querySelector('.sidebar-img').src = image.url;

    //publication.querySelector('.view-comments-button').href = `/image?id=${data.id}`;
    const postElement = publication.querySelector('.sidebar-post');
    postElement.setAttribute('data-post-id', image.id);
}

function deleteImage(id){
    authFetch('/api/image', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ "image_id": id})
    })
    .then(data => {
        displayMyImages(data);
    })
    .catch(error => {
        console.log(error);
        document.getElementById("commentError").textContent = error;
        document.getElementById("commentError").style.display= "block";
    });
}

function confirmationModal(postId, imageUrl){
    const modal = document.getElementById("myModal");
    const closebtn = document.getElementsByClassName("close")[0];
    const confirmBtn = document.getElementById("confirmBtn");
    const modalText = document.getElementById("modalText");
    const modalImg = document.getElementById("modalImg");

    modalText.innerText = `Do you want to delete this image?`;
    modal.style.display = "block";
    modalImg.src = imageUrl;

    closebtn.onclick = function() {
        modal.style.display = "none";
    }

    confirmBtn.onclick = function() {
        //alert(`Imagen confirmada:\nID: ${postId}\nURL: ${imageUrl}`);
        modal.style.display = "none";
        deleteImage(postId);
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
}

function deleteImageAttempt(event){
    const publicationElement = event.target.closest('.sidebar-post');
    const postId = publicationElement.getAttribute('data-post-id');
    const imageUrl = publicationElement.getElementsByClassName("sidebar-img")[0].src

    confirmationModal(postId, imageUrl);
   
}

function createPublicationElement(){
    const imageForSidebarTemplate = document.getElementById('imageForSidebar').content;
    return document.importNode(imageForSidebarTemplate, true);
}

function displayPublishedImage(image){
    const publication = createPublicationElement();
    setPublicationContent(publication, image);

    const deleteButton = publication.querySelector('.delete-button');
    deleteButton.addEventListener('click', deleteImageAttempt);

    publishSide.appendChild(publication);
}

function displayMyImages(images){
    
    publishSide.innerHTML = '';
    images.forEach(image => {
        displayPublishedImage(image);
    })
}


document.addEventListener('DOMContentLoaded', async () => {
    const images = await fetchMyImages();
    displayMyImages(images);
});

