<?php

require_once 'Settings.php';

error_reporting(0);


if(isset($_GET['wait'])){
    echo 'Biyografi Güncellenmesine : '.$_GET['wait']. ' Saniye Kaldı !';
    if($_GET['wait'] == 21){
        header("Refresh:1; url=WaitBiography.php?wait=20");
    }
    if($_GET['wait'] == 20){
        header("Refresh:1; url=WaitBiography.php?wait=19");
    }
    if($_GET['wait'] == 19){
        header("Refresh:1; url=WaitBiography.php?wait=18");
    }
    if($_GET['wait'] == 18){
        header("Refresh:1; url=WaitBiography.php?wait=17");
    }
    if($_GET['wait'] == 17){
        header("Refresh:1; url=WaitBiography.php?wait=16");
    }
    if($_GET['wait'] == 16){
        header("Refresh:1; url=WaitBiography.php?wait=15");
    }
    if($_GET['wait'] == 15){
        header("Refresh:1; url=WaitBiography.php?wait=14");
    }
    if($_GET['wait'] == 14){
        header("Refresh:1; url=WaitBiography.php?wait=13");
    }
    if($_GET['wait'] == 13){
        header("Refresh:1; url=WaitBiography.php?wait=12");
    }
    if($_GET['wait'] == 12){
        header("Refresh:1; url=WaitBiography.php?wait=11");
    }
    if($_GET['wait'] == 11){
        header("Refresh:1; url=WaitBiography.php?wait=10");
    }
    if($_GET['wait'] == 10){
        header("Refresh:1; url=WaitBiography.php?wait=9");
    }
    if($_GET['wait'] == 9){
        header("Refresh:1; url=WaitBiography.php?wait=8");
    }
    if($_GET['wait'] == 8){
        header("Refresh:1; url=WaitBiography.php?wait=7");
    }
    if($_GET['wait'] == 7){
        header("Refresh:1; url=WaitBiography.php?wait=6");
    }
    if($_GET['wait'] == 6){
        header("Refresh:1; url=WaitBiography.php?wait=5");
    }
    if($_GET['wait'] == 5){
        header("Refresh:1; url=WaitBiography.php?wait=4");
    }
    if($_GET['wait'] == 4){
        header("Refresh:1; url=WaitBiography.php?wait=3");
    }
    if($_GET['wait'] == 3){
        header("Refresh:1; url=WaitBiography.php?wait=2");
    }
    if($_GET['wait'] == 2){
        header("Refresh:1; url=WaitBiography.php?wait=1");
    }
    if($_GET['wait'] == 1 || $_GET['wait'] < 1){
        header("Refresh:1; url=SpotifyLogin.php");
    }
}
?>
