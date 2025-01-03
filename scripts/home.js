 document.addEventListener('DOMContentLoaded', function() {
            const token = localStorage.getItem('token');
            if (token){
                window.location.replace("/user/edit");
            }
        });


        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            fetch('/api/user/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email: email, password: password })
            })
            .then(response => response.json())
            .then(data => {
                if (data.token) {
                    localStorage.setItem('token', data.token);
                    window.location.replace("/user/edit");
                } else if (data.message) {
                    document.getElementById("loginFormBanner").textContent = data.message;
                    document.getElementById("loginFormBanner").style.visibility= "visible";
                    document.getElementById("loginForm").hidden = true;
                }
                else{
                    document.getElementById("loginFormBanner").textContent = "Try again";
                    document.getElementById("loginFormBanner").style.visibility= "visible";
                }
            })
            .catch(error => {
                document.getElementById("loginFormBanner").textContent = "Bad credentials, try again";
                document.getElementById("loginFormBanner").style.visibility= "visible";
            });
        });