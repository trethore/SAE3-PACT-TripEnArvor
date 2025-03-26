document.addEventListener("DOMContentLoaded", () => {
    const inputSearch = document.querySelector(".input-search");
    const datalist = document.querySelector("#cont");
    inputSearch.addEventListener("input", () => {
        const selectedOption = Array.from(datalist.options).find(
            option => option.value === inputSearch.value
        );
        if (selectedOption) {
            const idOffre = selectedOption.getAttribute("data-id");
            if (idOffre) {
                window.location.href = `/back/consulter-offre/index.php?id=${idOffre}`;
            }
        }
    });
    const options = Array.from(datalist.options).map(option => ({
        value: option.value,
        id: option.getAttribute("data-id")
    }));
 });