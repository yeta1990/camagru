async function loadTemplate(url, targetElementId) {
    try {
        const response = await fetch(url);
        const template = await response.text();
        document.getElementById(targetElementId).innerHTML = template;
        if (url.includes('header.html')) {
            addHeaderFunctionality();
        }
        if (url.includes('footer.html')) {
            addFooterFunctionality();
        }
    } catch (error) {
        console.error('Error loading template:', error);
    }
}

function addHeaderFunctionality() {
    const logoutButton = document.getElementById('logoutButton');
    if (logoutButton) {
        logoutButton.addEventListener('click', () => {
            localStorage.removeItem('token');
            window.location.href = '/login';
        });
    }
}

function addFooterFunctionality() {
}

document.addEventListener('DOMContentLoaded', () => {
    loadTemplate('/templates/header.html', 'headerContainer');
    loadTemplate('/templates/footer.html', 'footerContainer');
});
