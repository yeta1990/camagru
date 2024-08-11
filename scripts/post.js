
function setPostContent(post, data) {
    post.querySelector('.post-image').src = data.url;
    post.querySelector('.caption').textContent = data.caption;
    post.querySelector('.info').textContent = `${data.username} - ${new Date(data.date * 1000).toLocaleDateString()}`;

    post.querySelector('.view-comments-button').href = `/image?id=${data.id}`;
    const postElement = post.querySelector('.post');
    postElement.setAttribute('data-post-id', data.id);

    const tempElement = document.createElement('div');
    tempElement.innerHTML = post.querySelector('.caption').textContent;
    post.querySelector('.caption').textContent = tempElement.textContent;

}

function createPostElement() {
    const postTemplate = document.getElementById('postTemplate').content;
    return document.importNode(postTemplate, true);
}

function transformLikesText(likes){
    if (likes.length == 0){
        return "Nobody likes this image."
    }
    else if (likes.length == 1 && likes[0] == ""){
        return "Log in to see who likes this image."
    }
    let likesText = ""; 

    let hasUserLiked = false;

    if (likes.length == 1 && likes.includes("you")){
        return "You like this image."
    }
    else if (likes.length == 1){
        return "One user likes this image."
    }
    else if (likes.includes("you")){
        hasUserLiked = true;
        likes = likes.filter(username => username != "you");
        likesText = "You and " + likes.length + " more users like this image."
        return likesText;
    }
}

function displayLikes(likes, postElement) {
    const likesContainer = postElement.querySelector('.likes-container');
    while (likesContainer.firstChild) {
        likesContainer.removeChild(likesContainer.firstChild);
    }
    if (likes.includes("you")){
        postElement.querySelector(".fa-heart").classList.add("fa-solid");
        postElement.querySelector(".fa-heart").classList.remove("fa-regular");
        postElement.querySelector(".fa-heart").style.color = "red";
    }
    else {
        postElement.querySelector(".fa-heart").classList.remove("fa-solid");
        postElement.querySelector(".fa-heart").classList.add("fa-regular");
        postElement.querySelector(".fa-heart").style.color = "black";
    }

    const likesList = document.createElement('span');
    const likesText = transformLikesText(likes);
    likesList.innerText = likesText;
    likesContainer.appendChild(likesList);
}


function submitLike(event) {

        const postElement = event.target.closest('.post');
        const postId = postElement.getAttribute('data-post-id');

        authFetch('/api/image/like', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ "image_id": postId })
        })
        .then(data => {
            displayLikes(data.likes, postElement);
        })
        .catch(error => {
        });
}




function displayPost(data) {
    const post = createPostElement();
    setPostContent(post, data);

    const likeButton = post.querySelector('.like-button');
    displayLikes(data.likes, post);
    likeButton.addEventListener('click', submitLike);
    
    postContainer.appendChild(post);

}
