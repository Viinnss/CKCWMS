<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class Auth extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('session');
        // Load helper url untuk redirect dan curl untuk reCAPTCHA
        $this->load->helper('url');
    }
	
    public function index()
    {
        if ($this->session->userdata('email')) {
            redirect('user');
        }
        $data['title'] = 'Login Page';
        $data['background'] = base_url('assets') . '/img/auth/CKC.png';

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'required', [
            'required' => 'Please verify that you are not a robot.'
        ]);

        if ($this->form_validation->run() == false) {
            $this->load->view('templates/auth_header',$data);
            $this->load->view('auth/index',$data);
            $this->load->view('templates/auth_footer');
        } else {
            // validasi berhasil, cek throttling dan captcha
            // Cek login attempts
           $email = $this->input->post('email');
           $user = $this->db->get_where('users', ['Email' => $email])->row_array();

            if ($user && strtotime($user['blocked_until']) > time()) {
			$wait = ceil((strtotime($user['blocked_until']) - time()) / 60);
			$this->session->set_flashdata(
				'blocked',
				"<div class='alert alert-danger'>Your account is temporarily blocked for {$wait} minute(s) due to multiple failed login attempts.</div>"
			);
			redirect('auth');
			return;
		}

            // Verify Google reCAPTCHA
            $captcha_response = $this->input->post('g-recaptcha-response');
            if (!$this->verifyRecaptcha($captcha_response)) {
                $this->session->set_flashdata(
                    'captcha_error',
                    "<div class='alert alert-danger'>Captcha verification failed. Please try again.</div>"
                );
                redirect('auth');
                return;
            }

            // lanjut ke login process
            $this->_login();
        }
    }

	private function _login()
	{
		$email = $this->input->post('email');
		$password = $this->input->post('password');

		$user = $this->db->get_where('users', ['Email' => $email])->row_array();
	

		// jika usernya ada
		if ($user) {

			if ($user['blocked_until'] > time()) {
				$remaining = $user['blocked_until'] - time();
				$minutes = ceil($remaining / 60);
				$this->incrementLoginAttempts($email);
				$this->session->set_flashdata(
					'blocked_user',
					'<div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: 100%">
						<i class="bi bi-x-circle me-1"></i> Your account is blocked. Try again in ' . $minutes . ' minute(s).
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>'
				);
				redirect('auth');
			}
			// jika usernya aktif
			if ($user['Active'] == 1) {
				// cek password
				if (password_verify($password, $user['Password'])) {
					// Reset login attempts on successful login
					$this->db->where('Email', $email);
                    $this->db->update('users', [
                        'login_attempts' => 0,
                        'blocked_until' => 0
					]);

					$data = [
						'email' => $user['Email'],
						'name' => $user['Name'],
						'role_id' => $user['Role_id']
					];
					$this->session->set_userdata($data);

					if ($user['Role_id'] == 1) {
						redirect('adminhead/dashboard');
					} else {
						redirect('user');
					}
				} else {
					$this->incrementLoginAttempts($email);
					$this->session->set_flashdata(
						'wrong_password',
						'<div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: 100%">
							<i class="bi bi-x-circle me-1"></i> Your password is wrong!
							<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
						</div>'
					);
					redirect('auth');
				}
			} else {
				$this->session->set_flashdata(
					'not_active_email',
					'<div class="alert alert-success alert-dismissible fade show" role="alert" style="width: 40%">
						<i class="bi bi-x-circle me-1"></i> Your Email has not been activated
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>'
				);
				redirect('auth');
			}
		} else {
			$this->incrementLoginAttempts($email);
			$this->session->set_flashdata(
				'not_active_username',
				'<div class="alert alert-danger alert-dismissible fade show" role="alert" style="width: 100%">
					<i class="bi bi-x-circle me-1"></i> Your username is wrong
					<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
				</div>'
			);
			redirect('auth');
		}
	}

	private function incrementLoginAttempts($email)
	{
		$user = $this->db->get_where('users', ['Email' => $email])->row_array();

    if ($user) {

        // Kalau masih dalam masa blokir → langsung keluar
        if ($user['blocked_until'] > time()) {
            return;
        }

        // Kalau blokir sudah lewat → reset percobaan
        if ($user['blocked_until'] > 0 && time() > $user['blocked_until']) {
            $this->db->where('Email', $email);
            $this->db->update('users', [
                'login_attempts' => 1,
                'blocked_until' => 0
            ]);
            return;
        }

        $attempts = $user['login_attempts'] + 1;
        $data = ['login_attempts' => $attempts];

        if ($attempts >= 5) {
            $data['blocked_until'] = time() + (5 * 60);
        } else {
            $data['blocked_until'] = 0;
        }

        $this->db->where('Email', $email);
        $this->db->update('users', $data);
    }
}

	// verify reCAPTCHA response
	// This function sends a request to Google's reCAPTCHA API to verify the user's response
	private function verifyRecaptcha($captcha_response)
	{
		// reCAPTCHA secret key
		$secret = '6Lf6r6MrAAAAAJDW4F_LRzif3DKMBN8e8DzUQgS6'; 
		$verify_url = 'https://www.google.com/recaptcha/api/siteverify';

	    // Prepare data for POST request
		$data = [ 
			'secret' => $secret,
			'response' => $captcha_response
		];
		// Send request to Google reCAPTCHA API
		$options = [
			'http' => [
				'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
				'method'  => 'POST',
				'content' => http_build_query($data)
			]
		];

		// Create a stream context and send the request
		$context  = stream_context_create($options);
		$result = file_get_contents($verify_url, false, $context);

		// Check if the request was successful
		if ($result === FALSE) {
			return false;
		}

		$responseData = json_decode($result, true);

		// Check if the response is valid
		if (isset($responseData['success']) && $responseData['success'] == true && $responseData['score'] >= 0.7 && $responseData['action'] == 'login') {
			return true;
		}

		return false;
		
	}

	public function logout()
	{
		// Unset user session data
		$this->session->unset_userdata(['email', 'name', 'role_id']);
		$this->session->set_flashdata(
			'logout',
			'<div class="alert alert-danger alert-dismissible fade show" role="alert">
				You have been logout!
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>'
		);
		redirect('auth');
	}
	
}
