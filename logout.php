<?php
session_start();
session_unset();
session_destroy();

header("Location: login.php");
exit();
// ⚠️ ?> closing tag එක දාන්න එපා!
// ⚠️ HTML හෝ whitespace දාන්න එපා!