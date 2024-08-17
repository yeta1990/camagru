document.getElementById('editForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const token = urlParams.get("token");

    const password = document.getElementById('password').value;
    fetch('/api/user/changepass', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ token, password })
    })
    .then(response => {return {"status": response.status, "data": response.json()}})
    .then(async data => {
        if (data.status != 200){
            const message = (await data["data"]).message;
            return Promise.reject(new Error(message));
        }
        return data["data"];
    })
    .then(data => {
        document.getElementById("editForm").style.display= "none"
        document.getElementById("editFeedback").innerHTML= "<span>Edited successfully</span><br><a href='/home'>Go to login</a>";
        document.getElementById("editFeedback").style.visibility= "visible";
        document.getElementById("editForm").style.display= "none"
    })
    .catch(error => {
        document.getElementById("editFeedback").textContent = error;
        document.getElementById("editFeedback").style.visibility = "visible";
    });
});