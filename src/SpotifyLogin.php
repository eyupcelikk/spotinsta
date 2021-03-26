<?php
session_start();

error_reporting(0);

require_once '../vendor/autoload.php';

require_once '../vendor/instagram-private-api-php/Instagram.php';

require_once 'Settings.php';

$spotifyClientId = "fe7b29b4f5b4424098d440f938488f58";

$spotifyClientSecret = "d8b6de07f3aa4d7faefa68e825ce42b9";

$spotifyRedirectUri = "http://localhost/src/SpotifyLogin.php";

$session = new SpotifyWebAPI\Session(
    $spotifyClientId,
    $spotifyClientSecret,
    $spotifyRedirectUri
);

$API = new SpotifyWebAPI\SpotifyWebAPI();


if (isset($_GET['code'])) {
    $session->requestAccessToken($_GET['code']);
    $API->setAccessToken($session->getAccessToken());
    $options = [
        'scope' => [
            'user-read-email',
            'user-read-currently-playing',
            'user-read-playback-state'
        ],
    ];
    $currentMusic = $API->getMyCurrentPlaybackInfo($options);
    $currentMusic_Json = json_decode(json_encode($currentMusic), true);
    $currentMusic_Artist = $currentMusic_Json['item']['album']['artists'][0]['name'];
    $currentMusic_Music = $currentMusic_Json['item']['name'];

    if($currentMusic_Music == null && $currentMusic_Artist == null){
        echo 'Şarkı Bulunamadı !';
        echo '<br />';
        echo 'Veya Reklam Dinliyorsunuz !';
        session_destroy();
        echo '<br/>';
        echo '<a href = "SpotifyLogin.php"><button>Tekrar Dene</button></a>';
    }
    else{
        $biography = 'Şuan Dinliyor : '.$currentMusic_Artist.' | '.$currentMusic_Music.' 🎵 ';
        $_SESSION['Value'] = $biography;
        header('Location: BiographyEdit.php?Status=Ok');
    }


}
else {
    $options = [
        'scope' => [
            'user-read-email',
            'user-read-currently-playing',
            'user-read-playback-state'
        ],
    ];

    header('Location: ' . $session->getAuthorizeUrl($options));
    die();
}
