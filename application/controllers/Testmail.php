<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Testmail extends CI_Controller
{
    public function index()
    {
        $this->load->library('email');

        $this->email->from('inventorynotif@gmail.com', 'Inventory Notification');
        $this->email->to('kevinmarthakusuma03@gmail.com');
        $this->email->subject('Test Email CI');
        $this->email->message('Halo, ini tes kirim email dari CodeIgniter.');

        if ($this->email->send()) {
            echo "Email berhasil dikirim!";
        } else {
            echo $this->email->print_debugger();
        }
    }
}

