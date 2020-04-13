<?php
require_once ("../config/userclass.php");

require ("./sessionRedirect.php");

    $user = new User();
    $username = $_SESSION['user_session'];

    if (isset($_GET['pageno'])) {
      $pageno = $_GET['pageno'];
    } else {
      $pageno = 1;
    }
  
    $imagesPerPage = 6;
    $offset = ($pageno - 1) * $imagesPerPage;
  
    $query = $user->query("SELECT image_name FROM images");
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
      $picAvailabity = "Be the first to post";
    }
  
?>


<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Home: <?php print($_SESSION['user_session']) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.7.2/css/bulma.min.css">
  <link rel="stylesheet" href="../css/styles.css">
  <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
</head>

<body class="has-navbar-fixed-top">
  <?php include_once "navbar.php" ?>
  <div id="body-container" class="container is-fullhd">
    <section class="section is-fullwidth">
      <section name="gallery-images">
        <div class="container is-fluid" id="image-grid">
          <?php
						if(isset($picAvailabity))
							echo "<div class='notification is-primary is-light' style='width:80vw;'>
									
									'$picAvailabity'
								 </div>";
					?>
          <div id="Home-pics" class="columns is-multiline">
            <?php
						if(!empty($userPics)){
							foreach($userPics as $pic)
                	        {
                	            ?>
            <div class="column is-two-thirds">
              <div class="card">
                <div class="container is-fluid">
                  <div class="card-image">
                    <label><?php echo $username ?></label>
                    <figure id="card-image" class="image is-1by1 ">
                      <?php echo "<img id='square-img' src='../media/uploads/". $pic['image_name'] . "'>";?>
                    </figure>
                  </div>
                  <div class="card-content">
                    <form name="likes-comments" method="GET">
                      <article class="media">
                        <div class="media-content">
                          <div class="field">
                            <p class="control">
                              <textarea class="textarea" placeholder="Add a comment..."></textarea>
                            </p>
                          </div>
                          <nav class="level">
                            <div class="level-left">
                              <div class="level-item">
                                <a class="button is-info is-light">comment</a>
                              </div>
                            </div>
                          </nav>
                        </div>
                      </article>
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
                <a class="button is-rounded is-primary is-outlined is-small"
                  href="<?php if(totalImages == 0) { echo '#';} else echo '?pageno=1' ?>" aria-label="first Page">First
                  page</a>
              </li>
              <li>
                <a class="button is-rounded is-primary is-light is-small" <?php if($pageno <= 1){echo 'disabled';}?>
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
                  href="<?php if(totalImages == 0) { echo '#';} else echo '?pageno='.$totalPages ?>"
                  aria-label="last Page">Last Page
                </a>
              </li>
            </ul>
          </nav>
        </div>
      </section>
    </section>
    <div class="push"></div>
  </div>
  <?php var_dump($totalImages)?>
  <?php include "footer.php"?>
</body>

</html>