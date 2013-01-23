<?php

session_start();
//echo !isset($_SESSION['list']) . ' ';
if(!isset($_SESSION['list'])){
    $_SESSION['list'] = array();
    $_SESSION['type'] = array();
    //echo "array not set";
}

class Login extends CI_Controller {

    function validate_credentials(){
        $this->load->model('user_model');
        $query = $this->user_model->validate();
        
        if($query){ //if user credentials validated
            $data = array(
                'username' => $this->input->post('username'),
                'is_logged_in' => true
            );
            
            $this->session->set_userdata($data);

            //if user has navigated through hierarchy, send back to last level, else back to homepage
            if(isset($_SESSION["previouslevelink"]) && count($_SESSION["previouslevelink"]) >= 1){
                redirect($_SESSION["previouslevelink"][count($_SESSION["previouslevelink"])-1]);
            }else{
                redirect('inv/index'); 
            }           
        }else{
            $_SESSION['error_message'] = "Could not validate username and password. Please try again";
            //$data['error_message'] = "Could not validate username and password. Please try again";
            //$this->load->view('inv_view', $data);
            redirect('inv/index');
        }
    }
    function ajax_creds(){

        $this->load->model('user_model');
        $query = $this->user_model->validate();

        if($query){ //if user credentials validated

            $data = array(
                'username' => $this->input->post('username'),
                'is_logged_in' => true
            );
            $this->session->set_userdata($data);
            echo 'pass';
        }else{
            echo 'fail';
        }
    }
    function add_user(){
        $this->load->view('add_user');
    }
          
    function logout(){
            $this->session->sess_destroy();
            if(isset($_SESSION["previouslevelink"]) && count($_SESSION["previouslevelink"]) >= 1){
                redirect($_SESSION["previouslevelink"][count($_SESSION["previouslevelink"])-1]);
            }else{
                redirect('inv/index'); 
            } 
    }
           
} 