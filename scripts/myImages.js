
function fetchMyImages() {
    authFetch(`/api/image`)
        .then(data => {if (data.id == undefined) throw new Error(""); return data})
        .then(data => {
            console.log(data);
        })
        .catch(error => console.log(error)/*window.location.replace("/feed")*/);
}



document.addEventListener('DOMContentLoaded', () => {
    fetchMyImages();
});

