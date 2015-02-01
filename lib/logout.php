<?php
/************************************************
  If we're logging out we just need to clear our
  local access token in this case
 ************************************************/
unset($_SESSION['access_token']);
header("Location: http://" . $_SERVER['HTTP_HOST']);
exit();
?>