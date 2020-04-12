
<nav id="navnav"class="navbar is-fixed-top" role="navigation" aria-label="main navigation">
	<div class="navbar-brand">
		<a class="navbar-item" href="home.php">
			<img src="../media/logo/Logo.png" alt="Camagru Logo" width="70" style="max-height:100%;">
		</a>

		<a role="button" class="navbar-burger" data-target="navMenu" aria-label="menu" aria-expanded="false">
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
			<span aria-hidden="true"></span>
		</a>

	</div>
	<div id="navMenu" class="navbar-menu">
		<div class="navbar-start">
			<a class="navbar-item" href="gallery.php">
				Gallery
			</a>
			<a class="navbar-item" href="imageupload.php">
				Upload
			</a>
			<a class="navbar-item" href="profile.php">
				Profile
			</a>
		</div>

		<div class="navbar-end">

					<?php if(isset($username))
						echo "<a class='navbar-item has-text-grey' href='logout.php?logout=true'>
						<span class='icon'>
      						<i class='fas fa-sign-out-alt'></i>
						</span>
						<span>Log out</span></a>";
    	          	else 
						echo "<a class='navbar-item has-text-grey' href='../index.php'>
						<span class='icon'>
							<i class='fas fa-sign-in-alt'></i>
					 	</span>
						   <span><strong>Sign up</strong></span></a>";
    	    		?>

		</div>
	</div>
</nav>
<script src="../js/navBarMenu.js"></script>