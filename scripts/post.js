
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

function displayPost(data) {
    const post = createPostElement();
    setPostContent(post, data);

    postContainer.appendChild(post);

}