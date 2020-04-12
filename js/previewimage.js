//allows user to see a preview of the image they selected
function preview_image(event) 
{
 var reader = new FileReader();
 reader.onload = function()
 {
   var imgup = document.getElementById('image-upload');
   var output = document.getElementById('output_image');
   imgup.style.display = "block";
   console.log(event.target.files);
   output.src = reader.result;
 }
 if(event.target.files[0]){
   reader.readAsDataURL(event.target.files[0]);
 }
}