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
            window.location.href = '/home';
        });
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

    console.log("eooo");
    let newTheme = currentTheme === 'light' ? 'dark' : 'light';

    document.documentElement.setAttribute('data-theme', newTheme);
    if (newTheme == 'dark'){
        document.getElementById('toggleTheme').innerHTML = "<i class='fa-regular fa-lightbulb'></i>&nbsp;Turn off dark mode";
    }else {
        document.getElementById('toggleTheme').innerHTML = "<i class='fa-solid fa-moon'></i>&nbsp;Turn on dark mode";
    }
    localStorage.setItem('theme', newTheme);
}

function applyStoredTheme() {
    const storedTheme = localStorage.getItem('theme');
    if (storedTheme) {
        document.documentElement.setAttribute('data-theme', storedTheme);
        if (storedTheme == 'dark'){
            document.getElementById('toggleTheme').innerHTML = "<i class='fa-regular fa-lightbulb'></i>&nbsp;Turn off dark mode";
        }
        else {
            document.getElementById('toggleTheme').innerHTML = "<i class='fa-solid fa-moon'></i>&nbsp;Turn on dark mode";
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadTemplate('/templates/header.html', 'headerContainer');
    loadTemplate('/templates/footer.html', 'footerContainer');
    
});