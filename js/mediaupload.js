var canvas = document.getElementById('vid-canvas');
var context = canvas.getContext('2d');
var video = document.querySelector('video');

function add_sticker(sticker_src)
{
    var imageObj = new Image();
    
    imageObj.src = sticker_src;
    context.drawImage(imageObj, 0, 0, canvas.width/2, canvas.height/2);
    document.getElementById('vid-image').value = canvas.toDataURL('image/png');
}   
/**
 *  generates a still frame image from the stream in the <video>
 *  appends the image to the <body>
 */

 function retake(){
    video.style.display = 'block';
    canvas.style.display = 'none';
 }
function takeSnapshot() {
    
    canvas.height = video.offsetHeight;
    canvas.width = video.offsetWidth;
    console.log("taking snapshot" + video.offsetHeight);
    context.drawImage(video, 0, 0, canvas.width,canvas.height);
    document.getElementById('vid-image').value = canvas.toDataURL('image/png');
    video.style.display = 'none';
    canvas.style.display = 'block';
    console.log("try hiding");
}
// use MediaDevices API
// docs: https://developer.mozilla.org/en-US/docs/Web/API/MediaDevices/getUserMedia
if (navigator.mediaDevices)
{
    navigator.mediaDevices.getUserMedia({video: true})
    .then(function(stream) {
        /* use the stream */
        video.srcObject = stream;
        document.getElementById('vid-take').addEventListener("click", takeSnapshot);
    })
    .catch(function(err) {
        /* handle the error */
        alert("ERROR: could not access camera " + error.name);
    });
}