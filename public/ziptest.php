<?php
$fileName = "zipFile1";
$compressDir = __DIR__."\docs";
$zipFileSavePath = __DIR__."/";

$command =  "cd ". $compressDir .";"."zip -r ". $zipFileSavePath . $fileName .".zip .";

exec($command);

//header('Pragma: public');
//header("Content-Type: application/octet-stream");
//header("Content-Disposition: attachment; filename=".$fileName.".zip");
//readfile($zipFileSavePath.$fileName.".zip");
