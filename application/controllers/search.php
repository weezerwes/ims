<?php 

session_start();
if(!isset($_SESSION['list'])){
    $_SESSION['list'] = array();
    $_SESSION['type'] = array();
    //echo "array not set";
}

class Search extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('grocery_CRUD');
        //$this->is_logged_in();
    }
  
    function index() {
        $data = array();
        $_SESSION['list'] = array(); //clear breadcrumbs
        $_SESSION['idlist'] = array(); //clear id list
        $_SESSION['previouslevelink'] = array(); //clear links list
        $_SESSION['type'] = array(); //clear type list
        if (sizeof($_POST) >= 1) {
            $type = $_POST['type'];
            $search = $_POST['query'];
            if(isset($_POST['hierarchy_flag'])){
                $hierarchy_flag = $_POST['hierarchy_flag'];
            }else{
                $hierarchy_flag = 'false';
            }
            $this->load->model('inv_model');
            $data = $this->inv_model->search($type, $search, $hierarchy_flag);
            $data['type'] = $type;
        }
        $this->load->view('search_view', $data);
    }
    
}