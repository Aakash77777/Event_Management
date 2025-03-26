document.addEventListener("DOMContentLoaded", function () {
    const links = document.querySelectorAll(".sidebar ul li a");
    const content = document.querySelector(".main-content");

    links.forEach(link => {
        link.addEventListener("click", function (e) {
            e.preventDefault();
            const page = this.getAttribute("href");

            fetch(page)
                .then(response => response.text())
                .then(data => {
                    content.innerHTML = data; // Load content dynamically
                })
                .catch(error => console.error("Error loading page:", error));
        });
    });
});
