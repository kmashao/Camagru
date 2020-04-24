<?php
require_once ("../config/userclass.php");

require ("./sessionRedirect.php");

    $user = new User();
    $username = $_SESSION['user_session'];

    if($_GET['logout'] == "true")
    {
      $user->logOut();
      $user->redirect('../index.php');
    }

    if (isset($_GET['pageno'])) {
      $pageno = $_GET['pageno'];
    } else {
      $pageno = 1;
    }
  
    $imagesPerPage = 6;
    $offset = ($pageno - 1) * $imagesPerPage;
  
    $query = $user->query("SELECT image_name FROM images");
      $query->execute();
      $totalImages = $query->rowCount();
    $totalPages = ceil($totalImages / $imagesPerPage);
    
    //retreives images users have  uploaded
    $stmt = $user->query("SELECT * FROM images
              ORDER BY upload_date DESC LIMIT $offset, $imagesPerPage");
    $stmt->execute();
    $userPics = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(empty($userPics)){
      $picAvailabity = "Be the first to post, upload an image";
    }

    //like handler
    if(isset($_POST['like-btn'])){
        $stmt = $user->query("SELECT * FROM images WHERE image_name=:pic_name");
        $stmt->bindParam(":pic_name", $_POST['image'], PDO::PARAM_STR);
        $stmt->execute();
        $images = $stmt->fetchAll();
        $image = $images[0];

        $stmt = $user->query("SELECT * FROM likes WHERE image_id=:imageId AND username=:username");
        $stmt->bindParam("imageId",$image['image_id'], PDO::PARAM_STR);
        $stmt->bindParam("username",$username, PDO::PARAM_STR);
        $stmt->execute();
        //check if user already liked
        if($stmt->rowCount() < 1){
          $stmt = $user->query("INSERT INTO likes(image_id, username)
              VALUES(:image_id, :username)");
          $stmt->bindParam(":image_id",$image['image_id'], PDO::PARAM_STR);
          $stmt->bindParam(":username",$username, PDO::PARAM_STR);
          $stmt->execute();
        }else{
          $stmt = $user->query("DELETE FROM likes WHERE image_id=:imageId AND username=:userName");
          $stmt->bindParam(":imageId",$image['image_id'], PDO::PARAM_STR);
          $stmt->bindParam(":userName",$username, PDO::PARAM_STR);
          $stmt->execute();
        }
    }

    //comment handler
    if(isset($_POST['comment-btn'])){
      $comment = $user->test_input($_POST['comment']);

      if(empty($comment)){
        $commentErr = "comment cant be empty";
      }
      $stmt = $user->query("SELECT * FROM images WHERE image_name=:pic_name");
      $stmt->bindParam(":pic_name", $_POST['image'], PDO::PARAM_STR);
      $stmt->execute();
      $images = $stmt->fetchAll();
      $image = $images[0];

      //get user details from image
      $stmt = $user->query("SELECT * FROM users WHERE username=:username");
      $stmt->bindParam(":username", $image['username'], PDO::PARAM_STR);
      $stmt->execute();
      $details = $stmt->fetchAll();
      $notification = $details[0];

      // var_dump($notification);
      // die();
      
      //send email notification
      if($notification['notifications'] == "Yes" && $notification['username'] != $username){
        $headers = 'From:noreply@camagru.com' . "\r\n";
				$mail = "
				Good day". $notification['username'] ."
				$username commented on one of your posts.";

				mail($notification['email'],"Post update",$mail,$headers);
      }
      //add comment to db
      $stmt = $user->query("INSERT INTO comments(image_id, username, comment)
            VALUES(:image_id, :username, :comment)");
      $stmt->bindParam(":image_id",$image['image_id'], PDO::PARAM_STR);
      $stmt->bindParam(":username",$username, PDO::PARAM_STR);
      $stmt->bindParam(":comment", $comment, PDO::PARAM_STR);
      $stmt->execute();
      $user->redirect("home.php");
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
            <div class="column is-full">
              <div class="card">
                <div class="container is-fluid">
                  <div class="card-image">
                    <label class="subtitle is-5" style="padding:10px;">
                      <?php echo $pic['username']?>
                    </label>
                    <figure id="card-image" class="image is-1by1 ">
                      <?php echo "<img id='square-img' src='../media/uploads/". $pic['image_name'] . "'>";?>
                    </figure>
                  </div>
                  <div class="card-content">
                    <article class="media">
                      <div id="comments" class="media-content">
                        <?php
                                  $stmt = $user->query("SELECT * FROM comments WHERE image_id=:pic_id"); 
                                  $stmt->bindParam(":pic_id", $pic['image_id'], PDO::PARAM_STR);
                                  $stmt->execute();
                                  $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                  foreach($comments as $comm) {
                                  ?>
                        <p>
                          <strong><?php echo $comm['username']; ?>:</strong>
                          <br />
                          <p style="padding-left:10px;"><?php echo $comm['comment']; }?></p>
                        </p>
                      </div>
                    </article>
                    <form name="likes-comments" action="home.php" method="post"
                    <?php if($username == "guest"){echo "style='display: none;'";}?>>
                      <input type="hidden" name="image" value="<?php echo $pic['image_name'];?>">
                      <article class="media">
                        <div class="media-content">
                          <div class="field">
                            <p class="control">
                              <textarea class="textarea" name="comment" placeholder="Add a comment..."></textarea>
                            </p>
                          </div>
                          <nav class="level is-grouped">
                            <div class="level-left">
                              <div class="level-item">
                                <button name="comment-btn" class="button is-primary is-light is-small "
                                  value="OK" style="margin-right:25px;">comment
                                </button>

                                <button style="padding-left:10px;"name="like-btn" class="button is-small is-primary is-light" value="1">
                                  <span style="padding-right:10px;" class="icon is-small">
                                    <i class="fas fa-heart"></i>
                                  </span> like <?php
                                  $query = $user->query("SELECT * FROM likes WHERE image_id=:pic_id"); 
                                  $query->bindParam(":pic_id", $pic['image_id'], PDO::PARAM_STR);
                                  $query->execute();
                                  echo $query->rowCount();
                                     ?>
                                </button>
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
                <a class="button is-rounded is-primary is-outlined is-small is-left"
                  href="<?php if($totalImages == 0) { echo '#';} else echo '?pageno=1' ?>" aria-label="first Page">First
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
                <a class="button is-rounded is-primary is-outlined is-small -is-right"
                  href="<?php if($totalImages == 0) { echo '#';} else echo '?pageno='.$totalPages ?>"
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
  <?php var_dump($notification['email']) ?>
  <?php include "footer.php"?>
</body>

</html>