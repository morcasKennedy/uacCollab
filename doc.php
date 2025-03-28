a
<?php
    require 'app/module/functions/functions.php';

    $to = 'bienvenumoringa@gmail.com';
    $name = 'Immaculee';
    $sujet = 'Title';
    $content = 'Test from uac collab';
    if(Functions::send_mail($to, $name, $sujet, $content)) {
        print 'Mail envoyer';
    } else {
        print 'Mail non envoyer';
    }