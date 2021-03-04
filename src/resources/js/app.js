require('./bootstrap');

function fetchData() {
    fetch("http://localhost:8080/api/dino/locations")
        .then(response => {
            console.log(response);
            if (!response.ok) throw Error("Error");
            return response.json();
        })
        .then(data => {
            data.forEach(element => {
                let col = document.getElementById(element.location);
                if (element.is_safe) {
                    col.classList.add('dinoBackground');
                    col.innerHTML = "<img src='/storage/dino-parks-wrench.png' height='12px'>"
                }
            });
        })
        .catch(error => {
            console.log(error);
        });
}

fetchData();