<?php
session_start();
if (!isset($_SESSION['key']['usuario'])){ 
echo '1';
}else{
  echo '0';
}
?>