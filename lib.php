<?php
function sendFileToEmailWithTitleAndMessage($to, $subject, $message, $from, $file)
    {
    $baseName = basename($file);
    $base64Content =
    chunk_split(base64_encode(file_get_contents($file)));
    $boundary = md5(time());

    $headers = "From: ".$from."\n"
    ."MIME-Version: 1.0\n"
    ."Content-Type: multipart/mixed; boundary=\"".$boundary."\"";
    $content = ""
    ."This is a multi-part message in MIME format.\r\n"
    ."--".$boundary."\r\n"
    ."Content-type:text/plain; charset=iso-8859-1\r\n"
    ."Content-Transfer-Encoding: 7bit\r\n\r\n"
    .$message."\r\n\r\n"
    ."--".$boundary."\r\n"
    ."Content-Type: application/octet-stream;
    name=\"".$baseName."\"\r\n"
    ."Content-Transfer-Encoding: base64\r\n"
    ."Content-Disposition: attachment;
    filename=\"".$baseName."\"\r\n\r\n"
    .$base64Content."\r\n\r\n"
    ."--".$boundary."--";

    return mail($to, $subject, $content, $headers);
    }
?>