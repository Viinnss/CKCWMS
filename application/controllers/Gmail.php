<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// class Gmail extends CI_Controller
// {
//     public function __construct()
//     {
//         parent::__construct();
//     }

//     // Fungsi untuk generate token.json
//     public function auth()
//     {
//         require_once APPPATH . '../vendor/autoload.php';

//         $client = new Google_Client();
//         $client->setApplicationName('Inventory System');
//         $client->setScopes(Google_Service_Gmail::GMAIL_SEND);
//         $client->setAuthConfig(APPPATH . 'third_party/credentials.json');
//         $client->setAccessType('offline');
//         $client->setPrompt('select_account consent');

//         // 1. Buat URL Auth
//         $authUrl = $client->createAuthUrl();
//         echo "👉 Buka URL ini: <a href='$authUrl' target='_blank'>$authUrl</a><br>";

//         // 2. Jika sudah login & ada "code" dari Google → simpan token.json
//         if ($this->input->get('code')) {
//             $accessToken = $client->fetchAccessTokenWithAuthCode($this->input->get('code'));
//             file_put_contents(APPPATH . 'third_party/token.json', json_encode($accessToken));
//             echo "Token berhasil disimpan di application/third_party/token.json";
//         }
//     }
// }
