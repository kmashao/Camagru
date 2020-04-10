<?php
require_once ("../config/userclass.php");

require ("./sessionRedirect.php");

    $user = new User();
	$username = $_SESSION['user_session'];
	
	//checks and retreives the user has posted uploaded
	$stmt = $user->query("SELECT image_name FROM images WHERE username=:username
						ORDER BY upload_date DESC LIMIT 6");
	$stmt->bindparam(":username",$username, PDO::PARAM_STR);
	$stmt->execute();
	$userPics = $stmt->fetchAll(PDO::FETCH_ASSOC);
	if(empty($userPics)){
		$picAvailabity = "You currently have no pictures";
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
					<div class="columns is-multiline is-one-quarter-mobile">
						<?php
						foreach($userPics as $pic)
                	        {
                	            ?>
						<div class="column is-3">
							<div class="card" id="gallery-pics">
								<div class="container is-fluid">
									<div class="card-image">
										<figure id="card-image" class="image is-1by1 ">
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

			<div class="push"></div>
		</section>
	</div>
	<?php include_once "footer.php"?>
</body>

</html>