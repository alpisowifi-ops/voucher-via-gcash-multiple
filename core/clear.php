<?php
// ================= CONFIG =================
$file = "current.txt";

// ================= SAFETY =================
if(!file_exists($file)){
    file_put_contents($file, "0");
}

// ================= CLEAR WITH LOCK =================
$fp = fopen($file, "w");

if(flock($fp, LOCK_EX)){
    fwrite($fp, "0"); // reset value
    fflush($fp);
    flock($fp, LOCK_UN);
}

fclose($fp);

// ================= RESPONSE =================
echo "CLEARED";