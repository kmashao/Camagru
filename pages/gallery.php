<?php
require_once ("../config/userclass.php");

require ("./sessionRedirect.php");

    $user = new User();
	$username = $_SESSION['user_session'];


	//get current page number
	if (isset($_GET['pageno'])) {
		$pageno = $_GET['pageno'];
	} else {
		$pageno = 1;
	}

	$imagesPerPage = 6;
	$offset = ($pageno - 1) * $imagesPerPage;

	$query = $user->query("SELECT image_name FROM images WHERE username=:username");
    $query->bindParam(":username", $username, PDO::PARAM_STR);
    $query->execute();
    $totalImages = $query->rowCount();
	$totalPages = ceil($totalImages / $imagesPerPage);
	
	//checks and retreives images the user has  uploaded
	$stmt = $user->query("SELECT image_name FROM images WHERE username=:username
						ORDER BY upload_date DESC LIMIT $offset, $imagesPerPage");
	$stmt->bindparam(":username",$username, PDO::PARAM_STR);
	$stmt->execute();
	$userPics = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if(empty($userPics)){
		$picAvailabity = "You currently have no pictures";
	}

	//Deleting images frim database
	if(isset($_GET['delete-btn']) && !empty($_GET['imageData']))
	{	
		$imageID =0;
		$imageName = $user->test_input($_GET['imageData']);
		$imageIdArr = $user->getImageId($username, $imageName);
		$imageID = $imageIdArr['image_id']; 
		if($user->deleteImage($username,$imageID, $imageName)){
			$user->redirect('gallery.php');
			$deleteMsg = "Image successfully deleted";
		}else{
			$deleteMsg = "Couldn't delete image or image doesn't exist";
		}
	}
?>


<!DOCTYPE html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Gallery: <?php print($_SESSION['user_session']) ?></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.min.css">
	<link rel="stylesheet" href="../css/styles.css">
	<script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
	<script src="../js/notificationHide.js"></script>
</head>

<body class="has-navbar-fixed-top">
	<?php include "navbar.php" ?>
	<div id="body-container" class="container is-fullhd">
		<section class="section is-fullwidth">
			<div class="container">
			<?php
				if(isset($deleteMsg)){
					echo"<div class='modal is-active is-clipped'>
					<div class='modal-background'></div>
					<div class='modal-content'>
					  <div class='box'>
						<p class='text is-2 has-text-primary'>$picAvailabity</p>
					  </div>
					</div>
					<button class='modal-close is-large' aria-label='close'></button>
				  </div>
				  <script src='../js/modal.js'></script>";
				}
			?>
			
			</div>
			<section class="hero is-light">
				<div class="hero-body">
					<div class="container">
						<h4 class="title is-4">
							Here's you picture gallery <?php print($username) ?>
						</h4>
						<h5 class="subtitle">
							View and delete your pictures
						</h5>
					</div>
				</div>
			</section>
			<br />
			<section name="gallery-images">
				<div class="container is-fluid" id="image-grid">
					<?php
						if(isset($picAvailabity))
							echo "<div class='notification is-primary is-light' style='width:80vw;'>
									<button class='delete'></button>
									'$picAvailabity'
								 </div>";
					?>
					<div class="columns is-mobile is-multiline">
						<?php
						if(!empty($userPics)){
							foreach($userPics as $pic)
                	        {
                	            ?>
						<div class="column is-one-third-desktop is-one-third-tablet is-half-mobile">
							<div class="card" id="gallery-pics">
								<div class="container is-fluid">
									<div class="card-image">
										<figure id="card-image" class="image is-1by1 ">
											<?php echo "<img id='square-img' src='../media/uploads/". $pic['image_name'] . "'>";?>
										</figure>
									</div>
									<div class="card-content">
										<form action="gallery.php" name="delete-image" method="GET">
                              				<input type="hidden" id="image" name="imageData" value="<?php echo $pic['image_name'];?>">
											<button class="button is-info is-inverted" name="delete-btn" value=OK>Delete Image</button>
										</form>
									</div>
								</div>
							</div>
						</div>
						<?php
							}}
					?>
					</div>
				</div>
			</section>
			<section name="pagination">
				<div id="pagination" class="container is-mobile">
					<nav class="pagination is-rounded" role="navigation" aria-label="pagination">

						<ul class="pagination-list">
							<li>
								<a class="button is-rounded is-primary is-outlined is-small" href="?pageno=1"
									aria-label="first Page">First page</a>
							</li>
							<li>
								<a class="button is-rounded is-primary is-light is-small"
									<?php if($pageno <= 1){echo 'disabled';}?>
									href="<?php if($pageno <= 1) { echo '#';} else {echo '?pageno='.($pageno - 1);}?>">
									Previous
								</a>
							</li>
							<li>
								<a class="button is-rounded is-primary is-light is-small"
									<?php if($pageno >= $totalPages){echo 'disabled';}?>
									href="<?php if($pageno >= $totalPages) { echo '#';} else {echo '?pageno='.($pageno + 1);}?>">
									Next page
								</a>
							</li>


							<li>
								<a class="button is-rounded is-primary is-outlined is-small"
									href="?pageno=<?php echo $totalPages ?>" aria-label="last Page">Last Page
								</a>
							</li>
						</ul>
					</nav>
				</div>
			</section>

			<div class="push"></div>
		</section>
	</div>
	<?php var_dump($deleteMsg);?>
	<?php var_dump($imageID); ?>
	<?php include_once "footer.php"?>
</body>

</html>