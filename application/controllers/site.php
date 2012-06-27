<?php

class Site extends CI_Controller{

    function index(){
        $this->load->model('site_model');
        $data['country'] = $this->site_model->getAll();
        $data['city'] = $this->site_model->getCity();
        $data['building'] = $this->site_model->getBuilding();
        
        
        $data ['myValue'] = "Some string";
        $data ['anotherValue'] = 'Another string';
        $this->load->view('home', $data);
    }
    
    function about(){
        $this->load->view('about');
    }
    
}

?>
