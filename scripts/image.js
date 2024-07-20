
function getPostId() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

function createCommentElement() {
    const commentTemplate = document.getElementById('commentTemplate').content;
    return document.importNode(commentTemplate, true);
}

function displayComments(comments) {
    var paras = document.getElementsByClassName('comments');

    while(paras[0]) {
        paras[0].parentNode.removeChild(paras[0]);
    }

    const commentsContainer = document.createElement('div');
    commentsContainer.classList.add('comments');

    comments.forEach(commentData => {
        const comment = createCommentElement();
        setCommentContent(comment, commentData);
        commentsContainer.appendChild(comment);
    });

    postContainer.appendChild(commentsContainer);
}


function setCommentContent(comment, data) {
    comment.querySelector('.comment-username').textContent = data.username;
    comment.querySelector('.comment-text').textContent = data.comment;
    comment.querySelector('.comment-date').textContent = new Date(data.date * 1000).toLocaleDateString() + ' - ' + new Date(data.date * 1000).toLocaleTimeString();
}

function fetchPost(id) {
    authFetch(`/api/image?id=${id}`)
        .then(data => {if (data.id == undefined) throw new Error(""); return data})
        .then(data => {
            displayPost(data);
            displayComments(data.comments);
        })
        .catch(error => console.log(error)/*window.location.replace("/feed")*/);
}


document.getElementById('submitComment').addEventListener('click', submitComment);

document.addEventListener('DOMContentLoaded', () => {

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


        const postId = getPostId();
        authFetch('/api/image/comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ "image_id": postId, "comment": commentText})
        })
        .then(data => {
            displayComments(data.comments);
            document.getElementById('commentText').value = "";

        })
        .catch(error => {
            document.getElementById("commentError").textContent = error;
            document.getElementById("commentError").style.display= "block";
        });
        
    }
}

document.getElementById('submitComment').addEventListener('click', submitComment);
