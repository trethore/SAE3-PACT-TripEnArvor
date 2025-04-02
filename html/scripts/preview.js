function previewImage(event) {
    const preview = document.getElementById('preview');
    const imagePreview = document.getElementById('imagePreview');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader(); 
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            imagePreview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.src = '#';
        imagePreview.style.display = 'none';
    }
}

function previewImagePlan(event) {
    const preview = document.getElementById('preview');
    const imagePreview = document.getElementById('imagePreviewPlan');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader(); 
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            imagePreview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.src = '#';
        imagePreview.style.display = 'none';
    }
}

function previewImageCarte(event) {
    const preview = document.getElementById('preview');
    const imagePreview = document.getElementById('imagePreviewCarte');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader(); 
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            imagePreview.style.display = 'block';
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.src = '#';
        imagePreview.style.display = 'none';
    }
}