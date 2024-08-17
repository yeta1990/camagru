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
    }
}

function addHeaderFunctionality() {

    const logoutButton = document.getElementById('logoutButton');
    const editLinkButton = document.getElementById('editLink');
    const newPostLink = document.getElementById('newPostLink');

    if (logoutButton && localStorage.getItem("token") != null) {
        logoutButton.addEventListener('click', () => {
            localStorage.removeItem('token');
            window.location.href = '/home';
        });
    }
    else if (logoutButton && editLinkButton && newPostLink){
        logoutButton.style.display = 'none';
        editLinkButton.style.display = 'none';
        newPostLink.style.display = 'none';
    }
    const headerLink = document.getElementById('headerLink');
    if (headerLink && localStorage.getItem("token") != null){
        headerLink.style.display = 'none';
    }


}

function addFooterFunctionality() {
    const toggleThemeButton = document.getElementById('toggleTheme');
    toggleThemeButton.addEventListener('click', () =>{
       toggleTheme(); 
    })

    applyStoredTheme();
}



function toggleTheme() {
    let currentTheme = document.documentElement.getAttribute('data-theme');

    let newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    document.documentElement.setAttribute('data-theme', newTheme);
    if (newTheme == 'dark'){
        document.getElementById('toggleTheme').innerHTML = "<img src='/assets/lamp.png'  style='width:15px'/>&nbsp;Turn off dark mode";
    }else {
        document.getElementById('toggleTheme').innerHTML = "<img src='/assets/moon.png'  style='width:15px'/>&nbsp;Turn on dark mode";
    }
    localStorage.setItem('theme', newTheme);
    window.location.reload();
}

function applyStoredTheme() {
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme) {
        document.documentElement.setAttribute('data-theme', storedTheme);
        if (storedTheme == 'dark'){
            document.getElementById('toggleTheme').innerHTML = "<img src='/assets/lamp.png' style='width:15px'/>&nbsp;Turn off dark mode";
        }
        else {
        document.getElementById('toggleTheme').innerHTML = "<img src='/assets/moon.png'  style='width:15px'/>&nbsp;Turn on dark mode";
        }
    }
    else{
        document.getElementById('toggleTheme').innerHTML = "<img src='/assets/moon.png'  style='width:15px'/>&nbsp;Turn on dark mode";
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadTemplate('/templates/header.html', 'headerContainer');
    loadTemplate('/templates/footer.html', 'footerContainer');
    
});