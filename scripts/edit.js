document.addEventListener('DOMContentLoaded', function() {
     authFetch(`/api/user/whoami`)
         .then(data => {
             document.getElementById('id').value = data.id;
             document.getElementById('email').value = data.email;
             document.getElementById('username').value = data.username;
             const checkbox = document.getElementById('notif-checkbox');
             if (data.notifications){
                 checkbox.checked = true;
             }
             document.getElementById('notif-text').innerText = `Notifications by email ${data.notifications ? "enabled " : "disabled"}`;
         })
         .catch(error => {
         });

 });
 document.getElementById('notif-checkbox').addEventListener('change', function(event){
     event.preventDefault();
     authFetch('/api/user/notifications', {
         method: 'POST',
         headers: {
             'Content-Type': 'application/json'
         }
     })
     .then(data => {
         const checkbox = document.getElementById('notif-checkbox');
         if (data.notifications){
             checkbox.checked = true;
         }
         document.getElementById('notif-text').innerText = `Notifications ${data.notifications ? "enabled " : "disabled"}`;
     })
     .catch(error => {
         document.getElementById("editFeedback").textContent = error;
         document.getElementById("editFeedback").style.visibility = "visible";
     });
 })
 document.getElementById('editForm').addEventListener('submit', function(event) {
     event.preventDefault();
     const id = document.getElementById('id').value;
     const email = document.getElementById('email').value;
     const password = document.getElementById('password').value;
     const username = document.getElementById('username').value;
     authFetch('/api/user/edit', {
         method: 'POST',
         headers: {
             'Content-Type': 'application/json'
         },
         body: JSON.stringify({ id, email, username, password })
     })
     .then(data => {
         document.getElementById("editFeedback").textContent = "Edited successfully";
         document.getElementById("editFeedback").style.visibility= "visible";
     })
     .catch(error => {
         document.getElementById("editFeedback").textContent = error;
         document.getElementById("editFeedback").style.visibility = "visible";
     });
 });