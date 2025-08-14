<?php

function is_logged_in() 
{
	$ci = get_instance();
	// Check if the user is logged in
	if (!$ci->session->userdata('email')) {
		redirect('auth');
	} else {
		$menu = $ci->uri->segment(1);
		// If the user is logged in, check their access to the menu
		if($menu == 'user'){
			return true;
		}
		else{
			$role_id = $ci->session->userdata('role_id');
			
			if($menu == 'adminhead'){
				$menu_id = 1;
			}
			else{
				$queryMenu = $ci->db->get_where('user_menu', ['Name' => $menu])->row_array();
				$menu_id = $queryMenu['Id'];
			}
			// Get the user's access to the menu
			$userAccess = $ci->db->get_where('user_access_menu', [
				'Role_id' => $role_id,
				'Menu_id' => $menu_id
			]);
	
			// Check if the user has access to the menu
			if ($userAccess->num_rows() < 1) {
				redirect('auth/blocked');
			}
		}

	}
}

function check_access_submenu($role_id, $menu_id, $submenu_id)
{
	// Get the current CI instance
	$ci = get_instance();

	$ci->db->where('Role_id', $role_id);
	$ci->db->where('Menu_id', $menu_id);
	$ci->db->where('Submenu_id', $submenu_id);
	$result = $ci->db->get('user_access_submenu');

	if ($result->num_rows() > 0) {
		return "checked='checked'";
	}
}

function check_user_access($role_id, $menu_id, $submenu_id = null)
{
	// Get the current CI instance
	$CI = &get_instance();

	// Load the Access_model (if not already loaded)
	$CI->load->model('Access_model', 'ACModel');

	// Use the Access_model to verify access
	return $CI->ACModel->checkAccess($role_id, $menu_id, $submenu_id);
}

function perform_access_check()
{
	$CI = &get_instance();

	// Load models if not already loaded
	$CI->load->model('Access_model', 'ACModel');

	$user_session = $CI->db->get_where('users', [
		'Email' => $CI->session->userdata('email')
	])->row_array();
	$role_id = intval($user_session['Role_id']);

	$url_menu  = $CI->uri->segment(1);
	$menu_id   = $url_menu == 'adminhead' ? 1 : intval($CI->ACModel->getMenuId($url_menu));
	$url_submenu  = $CI->uri->segment(2);
	$submenu_id = intval($CI->ACModel->getSubMenuId($url_submenu));

	if (!check_user_access($role_id, $menu_id, $submenu_id)) {
		redirect('user');
	}
}
