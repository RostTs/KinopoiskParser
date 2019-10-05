<?php

include  "../../config/DB_conn.php";
include  "../../models/genres.php";

 $database = new Database;
 $db = $database->connect();

  $genresObj = new Genres($db);

  $all_genres = $genresObj->select();

  print_r($all_genres);
  $genres = json_encode($all_genres)?> 

  <form method="POST" action="../../index.php" name="Form">
  <input type='hidden' name='genres' value='<?php echo $genres; ?>'>
    </form> 

  <script type="text/javascript">
    document.Form.submit();
</script>