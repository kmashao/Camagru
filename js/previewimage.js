function preview_image(event) 
{
 var reader = new FileReader();
 reader.onload = function()
 {
    var imgup = document.getElementById('image-upload');
    var output = document.getElementById('output_image');
    imgup.style.display = "block";
    output.src = reader.result;
 }
 reader.readAsDataURL(event.target.files[0]);
}