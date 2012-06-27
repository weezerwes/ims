<?php

class Data_site extends CI_Controller{
    
    function index() {
        $this->load->model('data_model');
        $data['rows'] = $this->data_model->getMost();
        
        $this->load->view('data_home', $data);
    }
    
}

?>