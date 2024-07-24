
function setPostContent(post, data) {
    post.querySelector('.post-image').src = data.url;
    post.querySelector('.caption').textContent = data.caption;
    post.querySelector('.info').textContent = `${data.username} - ${new Date(data.date * 1000).toLocaleDateString()}`;

    post.querySelector('.view-comments-button').href = `/image?id=${data.id}`;
}

function createPostElement() {
    const postTemplate = document.getElementById('postTemplate').content;
    return document.importNode(postTemplate, true);
}


function displayLikes(likes, postElement) {
    const likesContainer = postElement.querySelector('.likes-container');
    while (likesContainer.firstChild) {
        likesContainer.removeChild(likesContainer.firstChild);
    }
    const likesList = document.createElement('span');
    likesList.innerText = likes;
    likesContainer.appendChild(likesList);
}


function submitLike(event) {

        const postElement = event.target.closest('.post');
        const postId = getPostId();
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
    console.log(data.likes);
    displayLikes(data.likes, post);
    likeButton.addEventListener('click', submitLike);
    
    postContainer.appendChild(post);

}
