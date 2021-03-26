<?php
// Created By Eyup Celik
require_once 'Constants/Constants.php';
require_once 'Exception/InstagramException.php';

class Instagram
{
  protected $username;            // Instagram Username
  protected $password;            // Instagram Password
  protected $debug;               // Debug

  protected $uuid;                // UUID
  protected $device_id;           // Devide ID
  protected $username_id;         // Username ID
  protected $token;               // _csrftoken
  protected $isLoggedIn = false;  // Session Status
  protected $rank_token;          // Rank Token
  protected $IGDataPath;
    public function __construct($username, $password, $debug = false, $IGDataPath = null)
  {
      $this->debug = $debug;
      $this->device_id = $this->generateDeviceId(md5($username.$password));

      $this->setUser($username, $password);
  }
    public function setUser($username, $password)
  {
      $this->username = $username;
      $this->password = $password;

      $this->uuid = $this->generateUUID(true);

  }
    public function login($force = false)
  {
      if (!$this->isLoggedIn || $force) {
          $fetch = $this->request('si/fetch_headers/?challenge_type=signup&guid='.$this->generateUUID(false), null, true);
          preg_match('#Set-Cookie: csrftoken=([^;]+)#', $fetch[0], $token);

          $data = [
          'phone_id'            => $this->generateUUID(true),
          '_csrftoken'          => $token,
          'username'            => $this->username,
          'guid'                => $this->uuid,
          'device_id'           => $this->device_id,
          'password'            => $this->password,
          'login_attempt_count' => '0',
      ];

          $login = $this->request('accounts/login/', $this->generateSignature(json_encode($data)), true);

          if ($login[1]['status'] == 'fail') {
              throw new InstagramException($login[1]['message']);

              return;
          }

          $this->isLoggedIn = true;
          $this->username_id = $login[1]['logged_in_user']['pk'];
          //file_put_contents($this->IGDataPath.$this->username.'-userId.dat', $this->username_id);
          $this->rank_token = $this->username_id.'_'.$this->uuid;
          preg_match('#Set-Cookie: csrftoken=([^;]+)#', $login[0], $match);
          $this->token = $match;
          //file_put_contents($this->IGDataPath.$this->username.'-token.dat', $this->token);

          return $login[1];
      }

  }
    public function editProfile($url, $phone, $first_name, $biography, $email, $gender)
  {
      $data = json_encode([
        '_uuid'         => $this->uuid,
        '_uid'          => $this->username_id,
        '_csrftoken'    => $this->token,
        'external_url'  => $url,
        'phone_number'  => $phone,
        'username'      => $this->username,
        'full_name'     => $first_name,
        'biography'     => $biography,
        'email'         => $email,
        'gender'        => $gender,
    ]);

      return $this->request('accounts/edit_profile/', $this->generateSignature($data))[1];
  }
    public function generateSignature($data)
    {

        $hash = hash_hmac('sha256', $data, Constants::IG_SIG_KEY);

        return 'ig_sig_key_version='.Constants::SIG_KEY_VERSION.'&signed_body='.$hash.'.'.urlencode($data);
    }
    public function generateDeviceId($seed)
    {
        // Neutralize username/password -> device correlation
        $volatile_seed = filemtime(__DIR__);

        return 'android-'.substr(md5($seed.$volatile_seed), 16);
    }
    public function generateUUID($type)
    {
        $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000,
      mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );

        return $type ? $uuid : str_replace('-', '', $uuid);
    }
    protected function request($endpoint, $post = null, $login = false)
    {
        if (!$this->isLoggedIn && !$login) {
            throw new InstagramException("Not logged in\n");

            return;
        }

        $headers = [
            'Connection: close',
            'Accept: */*',
            'Content-type: application/x-www-form-urlencoded; charset=UTF-8',
            'Cookie2: $Version=1',
            'Accept-Language: en-US',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, Constants::API_URL.$endpoint);
        curl_setopt($ch, CURLOPT_USERAGENT, Constants::USER_AGENT);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_VERBOSE, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->IGDataPath."$this->username-cookies.dat");
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->IGDataPath."$this->username-cookies.dat");

        if ($post) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }

        $resp = curl_exec($ch);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header = substr($resp, 0, $header_len);
        $body = substr($resp, $header_len);

        curl_close($ch);

        if ($this->debug) {
            echo "REQUEST: $endpoint\n";
            if (!is_null($post)) {
                if (!is_array($post)) {
                    echo 'DATA: '.urldecode($post)."\n";
                }
            }
            echo "RESPONSE: $body\n\n";
        }

        return [$header, json_decode($body, true)];
    }
}
