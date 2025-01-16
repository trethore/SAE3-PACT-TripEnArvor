document.addEventListener("DOMContentLoaded", function() {

    const showFormButton = document.getElementById('showFormButton');
    const avisForm = document.getElementById('avisForm');
    const cancelFormButton = document.getElementById('cancelFormButton');

    showFormButton.addEventListener('click', () => {
        avisForm.style.display = 'block'; 
        showFormButton.style.display = 'none';
    });

    cancelFormButton.addEventListener('click', () => {
        avisForm.style.display = 'none'; 
        showFormButton.style.display = 'block'; 
    });
});