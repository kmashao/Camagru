<?php

require_once "../config/userclass.php";
include_once "sessionRedirect.php";

$user = new User();
$username = $_SESSION['user_session'];

//Check and retrieve images recently uploaded

$stmt = $user->query("SELECT image_name FROM images WHERE username=:username
						ORDER BY upload_date DESC LIMIT 8");
$stmt->bindparam(":username",$username, PDO::PARAM_STR);
$stmt->execute();
$userPics = $stmt->fetchAll(PDO::FETCH_ASSOC);
if(empty($userPics)){
	$picAvailabity = "You currently have no pictures";
}

$uniqueId = uniqid();
$target_dir = "../media/uploads/";
$fileName =	basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . $uniqueId .$fileName;
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

//Handles uploads from device storage
if(isset($_POST["submit-btn"]) && !empty($_FILES["fileToUpload"]["tmp_name"]))
{
	
	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	$imageType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
	$allowed_types = array('jpg','png','jpeg','gif');

	// Check if image file is a actual image or fake image
	if($check != false) {
		$fileName =	basename($_FILES["fileToUpload"]["name"]);
	}
	else{
        $error = "File is not an image.";
	}


	if(!file_exists($target_dir)){
		mkdir($target_dir);
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 2000000) {
	    $error = "Sorry, your file is too large.";
	}

	// Allow certain file formats
	else if(!in_array($imageType, $allowed_types)) {
	    $error = "Only JPG, JPEG, PNG & GIF files are allowed.";
	}
	else
	{
		if(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
			if(is_uploaded_file($_FILES["fileToBeUploaded"]["tmp_name"]))
				$fileName =	basename($_FILES["fileToUpload"]["tmp_name"]);
			if($user->uploadImage($username,$uniqueId . $_FILES["fileToUpload"]["name"])){
				$success_msg = "Image " . $_FILES["fileToUpload"]["name"] ." successfully uploaded.";
				$user->redirect('imageupload.php');
			}else{
				$error = "There was an error uploading your image, please try again";
			}
		}else{
			$error = "There was an error uploding your image, please try again";
		}
	}
}else {
	$error = "No file chosen.";
}

/* Check if file already exists
if (file_exists($target_file)) {
    $error = "Sorry, file already exists.";
}*/

//Uploads from the webcam
if (isset($_POST['save-btn']))
{
	$data = $_POST['image_data'];
	$img = explode(',',$data);
	$decoded = base64_decode($img[1]);

	$imgDir = "../media/uploads/";
	$imgId = "cam_upload_" . uniqid();
	$fileName = $imgId . ".jpg";

	if(!file_exists($imgDir)){
		mkdir($imgDir);
	}

	if(file_put_contents($imgDir . $fileName, $decoded))
	{
		if($user->uploadImage($username,$fileName))
		{
			$success_msg = "$filename: has been successfully uploaded";
            $user->redirect('imageupload.php');
		}else{
			$error = "There was an error uploading your image, please try again";
		}
	}else{
		$error = "There was an error uploading your image, please try again";
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Image Upload</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
	<link rel="stylesheet" href="../css/styles.css">
	<script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>

	<script src="../js/previewimage.js"></script>
	<script src="../js/notificationHide.js"></script>

</head>

<body class="has-navbar-fixed-top">
	<?php include_once "navbar.php" ?>
	<div id="body-container" class="container is-fullhd">
		<section class="section is-fullwidth">
			<section class="hero is-primary is-bold" name="header">
				<div class="hero-body">
					<div class="container">
						<h1 class="title">
							Image Upload
						</h1>
					</div>
				</div>
			</section>
			<section id="upload-section">

				<p class="control">
					<label class="subtitle">Upload image from your gallery.</label>
				</p>
				<div class="field is-fluid">

					<form action="./imageupload.php" method="post" enctype="multipart/form-data">

						<?php	
		
							if(isset($error) && isset($_POST['submit-btn']))
								echo"<div class='help is-danger'>$error.</div>";
							else if(isset($success_msg) && isset($_POST['submit-btn']))
								echo"<div class='help is-success'>$success_msg.</div>"
							?>
						<div id="image-file" class="file">
							<label class="file-label">
								<input class="file-input" type="file" name="fileToUpload" accept="image/*"
									onchange="preview_image(event)" onchange="show_img()">
								<span class="file-cta">
									<span class="file-icon">
										<i class="fas fa-upload"></i>
									</span>
									<span class="file-label">
										Choose a file…
									</span>
								</span>
							</label>
						</div>

						<div class="container is-fluid" id="image-upload">
							<figure class="image is-1by1">
								<img id="output_image" alt="user-image" />
							</figure>
						</div>

						<!--			<script src="../js/filename.js"></script>-->
							<button type="submit" class="button is-primary is-light" name="submit-btn">
								Upload Image.
							</button>
					</form>
				</div>
			</section>
			<br>
			<hr>
			<section name="snap-section">
				<div class="container is-fluid">
					<label class="subtitle"> Click the stickers to add to your image below</label>
					<div class="columns is-mobile" style="overflow:hidden; height:200px;">
						<div class="column">
							<img id="sticker" src="../media/stickers/chibi.png" onclick="add_sticker(src)">
						</div>
						<div class="column">
							<img id="sticker" src="../media/stickers/cute_doggo.png" onclick="add_sticker(src)">
						</div>
						<div class="column">
							<img id="sticker" src="../media/stickers/whiskers.png" onclick="add_sticker(src)">
						</div>
						<div class="column">
							<img id="sticker" src="../media/stickers/winged_heart.png" onclick="add_sticker(src)">
						</div>
					</div>
				</div>
				<label class="subtitle"> Time to take a photo say cheese</label>
				<div id="vid-divs">

					<div id="vid-container" class="container" style="margin-right: auto;margin-left: auto;">
						<video  autoplay id="video"></video>
						<canvas id="vid-canvas"></canvas>
						<script src="../js/mediaupload.js"></script>
					
					</div>
					<br>
					<p class="control">
						<div class="field is-grouped">
							<p class="control">
								<button class="button is-link is-light is-small" id="vid-take">Take picture</button>
							</p>
							<p class="control">
								<button class="button is-warning is-light is-small" id="vid-retake"
									onclick="retake()">Not
									satisfied</button>
							</p>
							<p class="control">
								<form name="cam-image" method="post">
									<input type="hidden" id="vid-image" name="image_data">
									<button class="button is-primary is-light is-small" type="submit" id="save-btn"
										name="save-btn" value="OK" >Save</button>
								</form>
							</p>
						</div>
					</p>
				</div>
			</section>
			<hr />


			<section name="recent-images">
				<div class="container is-fluid" name="image-grid">
					<label class="subtitle is 6"> Here are your recent photos</label>
					<br />
					<?php
						if(isset($picAvailabity))
							echo "<div class='notification is-primary is-light'>
									<button class='delete'></button>
									'$picAvailabity'
								 </div>";
					?>
					<div class="columns is-mobile is-multiline">
						<?php
						foreach($userPics as $pic)
                	        {
                	            ?>
						<div class="column is-one-quarter-desktop is-one-quarter-tablet is-half-mobile">
							<div class="card" id="gallery-pics">
								<div class="container">
									<div class="card-image">
										<figure id="card-image" class="image is-1by1" >
											<?php echo "<img id='square-img' src='../media/uploads/". $pic['image_name'] . "'>";?>
										</figure>
									</div>
								</div>
							</div>
						</div>
						<?php
							}
					?>
					</div>
				</div>
			</section>
		</section>
		<div class="push"></div>
	</div>
	<?php include_once "footer.php" ?>
</body>

</html>