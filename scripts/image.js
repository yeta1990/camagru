
function getPostId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

function fetchPost(id) {
    authFetch(`/api/image?id=${id}`)
        .then(data => {
            displayPost(data);
            //displayComments(data.comments);
        })
        .catch(error => console.error('Error fetching post:', error));
}

function setPostContent(post, data) {
    post.querySelector('.post-image').src = data.url;
    post.querySelector('.caption').textContent = data.caption;
    post.querySelector('.info').textContent = `${data.username} - ${new Date(data.date * 1000).toLocaleDateString()}`;
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

document.getElementById('submitComment').addEventListener('click', submitComment);

document.addEventListener('DOMContentLoaded', () => {

    const postContainer = document.getElementById('postContainer');
    const commentTemplate = document.getElementById('commentTemplate').content;
    const postId = getPostId();
    fetchPost(postId);
});


function submitComment() {
    const commentText = document.getElementById('commentText').value;
    const commentError = document.getElementById('commentError');

    if (commentText.length < 1 || commentText.length > 256) {
        commentError.style.display = 'block';
    } else {
        commentError.style.display = 'none';

        console.log(commentText);

        const postId = getPostId();
        authFetch('/api/image/comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ "image_id": postId, "comment": commentText})
        })
        .then(data => {
            /*document.getElementById("signupFeedback").textContent = "Created successfully. Before login, you must confirm your account, check your mail!";
            document.getElementById("signupFeedback").style.visibility = "visible";
            */
        })
        .catch(error => {
            document.getElementById("commentError").textContent = error;
            document.getElementById("commentError").style.display= "block";
        });
        
        // Aquí iría el código para enviar el comentario al backend.
        console.log('Comentario enviado:', commentText);
    }
}

document.getElementById('submitComment').addEventListener('click', submitComment);
