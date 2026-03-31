jQuery(document).ready(function($) {
    $('#uploadform_PDF').on('submit', function(event) {
    event.preventDefault(); // Stop the default form submission
    const post_upload = $('#key_upload_font')[0];
    const file = post_upload.files[0];
    if (file) {
        const allowed_size = 1 * 1024 * 1024 * 1024;
        if (file.size > allowed_size) {
            alert('File too big! Maximum 1GB only!');
        } else {
            $('#notehere').html("uploading...");
            $('#btn_upload').remove();
            $('#progress_bar_container').show(); // Show the progress bar
            // Use FormData to create a payload for the AJAX request
            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            // Listen for the upload progress event
            xhr.upload.addEventListener('progress', function(event) {
                if (event.lengthComputable) {
                    const percent = Math.round((event.loaded / event.total) * 100);
                    $('#progress_bar').css('width', percent + '%').html(percent + '%');
                }
            });
            // Listen for when the upload is complete
            xhr.addEventListener('load', function() {
              window.location.href='./index.php?req=doc';
            });
            // Listen for any errors
            xhr.addEventListener('error', function() {
                $('#notehere').html("An error occurred during upload.");
                setTimeout(function() {
                    window.location.href='./index.php?req=doc';
                }, 5000);
            });
            // Send the file to server
            xhr.open('POST', 'Element/File/fileact.php?reqact=addfile');
            xhr.send(formData);
        }
    }
  });
});

window.addEventListener('load', () => {
    const activeItem = document.querySelector('.Sidebar .active');
    
    if (activeItem) {
        activeItem.scrollIntoView({ 
            behavior: 'auto',
            block: 'nearest' 
        });
    }
});