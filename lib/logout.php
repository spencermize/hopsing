<?php
/************************************************
  If we're logging out we just need to clear our
  local access token in this case
 ************************************************/
$app->deleteCookie("_hopsauth");
$app->redirect('http://' . $_SERVER['HTTP_HOST']);
?>