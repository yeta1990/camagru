
const LIMIT_PAGE = 3;

function createPagination(totalPages) {
    
    const paginationDiv = document.getElementById('pagination');
    
    for (let i = 1; i <= totalPages; i++) {
            paginationDiv.appendChild(createPageLink(i));
    }
}

function createPageLink(page) {
    const link = document.createElement('a');
    link.href = `?page=${page}`;
    link.textContent = page;
    link.style.margin = '0 5px';
    return link;
}

function getCurrentPage() {
    const urlParams = new URLSearchParams(window.location.search);
    return parseInt(urlParams.get('page')) || 1;
}

async function fetchImages(page) {
    const images = await authFetch(`/api/image/feed?page=${page}&limit=${LIMIT_PAGE}`)
        .then(data => data)
        .catch(error => console.error('Error fetching images:', error));
    return images;
}

function displayImages(images) {
    feedContainer.innerHTML = '';
    images.forEach(image => {
        const post = createPostElement();
        setPostContent(post, image);
        feedContainer.appendChild(post);
    });
}

function createPostElement() {
    const postTemplate = document.getElementById('postTemplate').content;
    return document.importNode(postTemplate, true);
}

function setPostContent(post, image) {
    post.querySelector('.post-image').src = image.url;
    post.querySelector('.caption').textContent = image.caption;
    post.querySelector('.info').textContent = `${image.username} - ${new Date(image.date * 1000).toLocaleDateString()}`;
}


document.addEventListener('DOMContentLoaded', async () => {

    const totalPages = await authFetch(`/api/image/pages?limit=${LIMIT_PAGE}`)
        .then(data => data)
        .catch(error => {
            console.error('Error:', error);
        });

    const currentPage = getCurrentPage();
    
    createPagination(totalPages);
    const images = await fetchImages(currentPage);
    displayImages(images);
});