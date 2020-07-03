<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller
{
    public function get($username = NULL)
    {
        $this->load->library('user/lib_user_login');
        $logged_in_user = $this->lib_user_login->get_user();
        
        if (empty($username))
        {
            $user = $logged_in_user;
        }
        else
        {
            $this->load->library('user/lib_user');
            $user = $this->lib_user->get_by_username($username, $logged_in_user['user_id']);
        }
        
        header('Content-Type: application/json');
        print_r( json_encode($user) ); 
        die();
    }

    public function email($username = 0)
    {
        $visit_user_id = 0;
        $this->load->library('user/lib_user_login');
        $logged_in_user = $this->lib_user_login->get_user();

        if (!empty($logged_in_user)) $visit_user_id = $logged_in_user['user_id'];

        $this->load->library('user/lib_user');    
        $req_user = $this->lib_user->get_by_username($username, $visit_user_id);

        if (empty($req_user))
        {
            show_error('Invalid username');
        }

        $this->load->library('user/lib_user_email');
        $req_user_email = $this->lib_user_email->get_email_by_id($req_user['user_id']);

        header('Content-Type: application/json');
        print_r( json_encode($req_user_email) ); 
        die();
    }
}