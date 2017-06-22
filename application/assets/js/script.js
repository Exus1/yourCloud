




$(document).ready(function() {
  var ycDropzone = new Dropzone('body',
  {
    url: window.location.href,
    clickable: '#upload-button',
    autoQueue: true,
    previewsContainer: '#upload-box',
    createImageThumbnails: false,
    previewTemplate: Your_cloud.dropzone_file_template
  });

  ycDropzone.on('thumbnail', function(file) {
    return;
  });

  ycDropzone.on('success', function(file, response) {
    $(file.previewElement).find('.progress-bar').addClass('bg-success');
    $(file.previewElement).find('.progress-bar').removeClass('progress-bar-animated');

    Your_cloud.refresh_objects();
  });

  ycDropzone.on('error', function(file, message) {
  	$(file.previewElement).find('.progress-bar').addClass('bg-danger');
    $(file.previewElement).find('.progress-bar').removeClass('progress-bar-animated');
    alert(message);
  });

  ycDropzone.on('uploadprogress', function(file, progress, bytesSent) {
  	$(file.previewElement).find('.progress-bar').width(progress + '%');
  });

  ycDropzone.on('canceled', function(file) {
  	$(file.previewElement).find('.progress-bar').addClass('bg-warning');
    $(file.previewElement).find('.progress-bar').removeClass('progress-bar-animated');

    alert('cancel');
  });
});
