<?php

session_start();
//echo !isset($_SESSION['list']) . ' ';
if(!isset($_SESSION['list'])){
    $_SESSION['list'] = array();
    $_SESSION['type'] = array();
    //echo "array not set";
}

class Admin extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('grocery_CRUD');
        //$this->load->library('grocery_CRUD_extended');        
        $this->is_logged_in();
    }
    
    function index(){
        redirect('admin/edit'); 
    }
    
    function is_logged_in(){
        $logged_in = $this->session->userdata('is_logged_in');
        
        //views that allow editing data have _admin extension. 
        if(!isset($logged_in) || $logged_in == FALSE){
            echo 'You don\'t have permission to access this page. <br><a href="' . base_url() . '">Login</a>';
            die();
        }
    }    
    //get id of parent 2 levels up to filter it's child based on current hierarchy
    //this filters the dropdown to limit options of relation to current hierarchy
    public function get_relation_id($type){
        if(sizeof($_SESSION['idlist']) >= 2 && end($_SESSION['type']) == $type){
            return $_SESSION['idlist'][sizeof($_SESSION['idlist'])-2];
        }else{
            return null;
        }
    }  
    
    //get id of parent one level above to automatically populate relation dropdown
    public function get_one_level_up_relation_id($type){
        if(sizeof($_SESSION['idlist']) >= 1 && end($_SESSION['type']) == $type){
            return $_SESSION['idlist'][sizeof($_SESSION['idlist'])-1];
        }else{
            return null;
        }
        
    }

    public function country(){
        //$this->grocery_crud->set_theme('datatables');
        
        $this->grocery_crud->set_table('country');
        $this->grocery_crud->set_subject('Country');
        $this->grocery_crud->required_fields('country_name');
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }
    
    public function city(){
        $this->grocery_crud->set_table('city');
        $this->grocery_crud->set_subject('City');
        $id = $this->get_one_level_up_relation_id('city');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('country_id', 'country', 'country_name', array('country_id' => $id));
        }else{
            $this->grocery_crud->set_relation('country_id', 'country', 'country_name'); 
        }          
        $this->grocery_crud->display_as('country_id', 'Country');
        $this->grocery_crud->required_fields('country_id', 'city_name', 'state_name');
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    

    public function building(){
        $this->grocery_crud->set_table('building');
        $this->grocery_crud->set_subject('Building');   
        $id = $this->get_one_level_up_relation_id('building');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('city_id', 'city', 'city_name', array('city_id' => $id));
        }else{
            $this->grocery_crud->set_relation('city_id', 'city', 'city_name'); 
        }         
        $this->grocery_crud->display_as('city_id', 'City');
 
        $this->grocery_crud->required_fields('city_id', 'building_address', 'building_zip');        
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    
    
    public function location(){
        $this->grocery_crud->set_table('location');
        $this->grocery_crud->set_subject('Location');        
        $this->grocery_crud->display_as('external_entity_id', 'Controlling Party');
        $this->grocery_crud->set_relation('external_entity_id', 'external_entity', 'external_entity_name'); 
        $id = $this->get_relation_id('location');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('building_id', 'building', 'building_address', array('city_id' => $id));
        }else{
            $this->grocery_crud->set_relation('building_id', 'building', 'building_address');   
        }        
        $this->grocery_crud->display_as('building_id', 'Building');
        $this->grocery_crud->required_fields('building_id', 'location_name');        
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    
    
    public function aisle(){
        $this->grocery_crud->set_table('aisle');
        $this->grocery_crud->set_subject('Aisle');        
        $this->grocery_crud->display_as('location_id', 'Location');
        $id = $this->get_relation_id('aisle');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('location_id', 'location', 'location_name', array('building_id' => $id)); 
        }else{
            $this->grocery_crud->set_relation('location_id', 'location', 'location_name');
        }        
        //$this->grocery_crud->set_relation('location_id', 'location', 'location_name');  
        $this->grocery_crud->required_fields('location_id', 'aisle_number');        
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    
    
    public function bay(){
        $this->grocery_crud->set_table('bay');
        $this->grocery_crud->set_subject('Bay');        
        $id = $this->get_relation_id('bay');
        $this->grocery_crud->display_as('aisle_id', 'Aisle');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('aisle_id', 'aisle', 'aisle_number', array('location_id' => $id)); 
        }else{
            $this->grocery_crud->set_relation('aisle_id', 'aisle', 'aisle_number');             
        }
        $this->grocery_crud->display_as('bay_type_id', 'Bay Type');
        $this->grocery_crud->set_relation('bay_type_id', 'bay_type', 'bay_type');  
        $this->grocery_crud->set_relation('bay_id', 'bay_nickname', 'bay_nickname'); 
        $this->grocery_crud->required_fields('aisle_id', 'bay_number', 'bay_height', 'bay_type_id');        
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    
    
    public function bay_nickname(){
        $this->grocery_crud->set_table('bay_nickname');
        $this->grocery_crud->set_subject('Bay Nickname');        
        $this->grocery_crud->display_as('bay_id', 'Bay');
        $this->grocery_crud->set_relation('bay_id', 'bay', 'bay_number');     
        $this->grocery_crud->required_fields('bay_id', 'bay_nickname');        
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }     
    
    public function bay_type(){
        $this->grocery_crud->set_table('bay_type');
        $this->grocery_crud->set_subject('Bay Type');  
        $this->grocery_crud->required_fields('bay_type');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }     
    
    public function shelf(){
        $this->grocery_crud->set_table('shelf');
        $this->grocery_crud->set_subject('Shelf');        
        $this->grocery_crud->display_as('chassis_type_id', 'Chassis Type');
        $this->grocery_crud->set_relation('chassis_type_id', 'chassis_type', 'chassis_type');
        $id = $this->get_relation_id('shelf');
        $this->grocery_crud->display_as('bay_id', 'Bay');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('bay_id', 'bay', 'bay_number', array('aisle_id' => $id));
        }else{
            $this->grocery_crud->set_relation('bay_id', 'bay', 'bay_number');     
        }
        $this->grocery_crud->required_fields('bay_id', 'shelf_name', 'top_rack_unit', 'chassis_type_id');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    
    
    public function chassis_type(){
        $this->grocery_crud->set_table('chassis_type');
        $this->grocery_crud->set_subject('Chassis Type');  
        $this->grocery_crud->change_field_type('terminating', 'enum', array('Non-Terminating', 'Terminating'));
        $this->grocery_crud->display_as('manufacturer_id', 'Manufacturer');
        $this->grocery_crud->set_relation('manufacturer_id', 'manufacturer', 'manufacturer_name');
        $this->grocery_crud->display_as('platform_id', 'Platform');
        $this->grocery_crud->set_relation('platform_id', 'platform', 'platform');        
        $this->grocery_crud->required_fields('chassis_type', 'number_of_slots', 'height', 'terminating', 'manufacturer_id', 'platform_id');                  
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }  

    public function slot(){
        $this->grocery_crud->set_table('slot');
        $this->grocery_crud->set_subject('Slot');            
        $id = $this->get_one_level_up_relation_id('slot');
        $this->grocery_crud->display_as('shelf_id', 'Shelf');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('shelf_id', 'shelf', 'shelf_name', array('shelf_id' => $id)); 
        }else{
            $this->grocery_crud->set_relation('shelf_id', 'shelf', 'shelf_name'); 
        }  
        $this->grocery_crud->display_as('slot_type_id', 'Slot Type');
        $this->grocery_crud->set_relation('slot_type_id', 'slot_type', 'slot_type');  
        //check that slot is same platform as parent shelf
        $this->grocery_crud->set_rules('slot_type_id', 'slot_type','callback_check_shelf_platform');  
        $this->grocery_crud->set_rules('shelf_id', 'shelf', 'callback_check_shelf_number_of_open_slots');
        $this->grocery_crud->callback_after_insert(array($this, 'create_ports_and_sub_ports'));
        $this->grocery_crud->callback_before_delete(array($this, 'delete_ports_and_sub_ports'));        
        $this->grocery_crud->required_fields('slot_type_id', 'shelf_id', 'slot_number');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    } 
    
    function check_shelf_platform() {
        $child_object = null;
        $parent_object = null;
        $this->load->model('inv_model');        
        if (isset($_POST['slot_type_id'])) { //if creating a slot, pull slot type platform and chassis type platform
            $child_object = 'slot';
            $parent_object = 'shelf';      
            $q1 = $this->inv_model->get_slot_platform_from_slot_type($_POST['slot_type_id']);
            $q2 = $this->inv_model->get_shelf_platform($_POST['shelf_id']);
        } else if (isset($_POST['sub_slot_type_id'])) { //if creating a sub slot, pull sub slot type platform and slot type platform
            $child_object = 'sub slot';
            $parent_object = 'slot';           
            $q1 = $this->inv_model->get_sub_slot_platform_from_sub_slot_type($_POST['sub_slot_type_id']);
            $q2 = $this->inv_model->get_slot_platform_from_slot_id($_POST['slot_id']); 
        }

        $state = $this->grocery_crud->getState();
        if ($q1->num_rows() > 0 && $q2->num_rows() > 0 && ($state == 'insert_validation' || $state == 'update_validation')) {
            $child_row_platform = $q1->row()->platform;
            $parent_row_platform = $q2->row()->platform;
            if ($child_row_platform != $parent_row_platform) {
                $this->form_validation->set_message('check_shelf_platform', 'The new ' . $child_object . ' platform (' . $child_row_platform . ') must match the ' . $parent_object . ' platform (' . $parent_row_platform . ')');
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }
    
    function check_shelf_number_of_open_slots() {
        $this->load->model('inv_model');
        $open_shelf_slots = $this->inv_model->get_number_of_open_slots_of_shelf($_POST['shelf_id']);
        if ($open_shelf_slots < 1) {
            $this->form_validation->set_message('check_shelf_number_of_open_slots', 'Shelf does not have any open slots remaining');
            return FALSE;
        }
    }
    
    function create_ports_and_sub_ports($post_data, $insert_primary_key){
        $state_info = $this->grocery_crud->getStateInfo();
        if(isset($state_info->primary_key)){
            $primary_key = $state_info->primary_key;
        }else{
            $primary_key = $insert_primary_key;
        }
        if(isset($_POST['slot_type_id'])){
            $this->load->model('inv_model');
            $this->inv_model->create_ports_for_new_slot($_POST['slot_type_id'], $primary_key);
        }else{
            show_error('Cannot create ports without valid slot type');
        }
    }
    
    function delete_ports_and_sub_ports($primary_key){
        if(isset($primary_key)){
            $this->load->model('inv_model');
            $this->inv_model->delete_all_ports_for_slot($primary_key);
        }else{
            show_error('Cannot delete ports without valid slot id');
        }        
    }
    
    public function slot_type(){
        $this->grocery_crud->set_table('slot_type');
        $this->grocery_crud->set_subject('Slot Type');   
//        $this->grocery_crud->change_field_type('port_numbering','dropdown',
//            array('Horizontal' => 'Horizontal', 'Vertical' => 'Vertical'));
        $this->grocery_crud->change_field_type('slot_orientation', 'enum', array('Vertical','Horizontal'));        
        $this->grocery_crud->change_field_type('port_numbering', 'enum', array('Horizontal','Vertical'));
        $this->grocery_crud->change_field_type('number_of_sub_slots', 'enum', array(0,1,2,3,4,5,6,7,8));  
        $this->grocery_crud->set_relation('circuit_type_id', 'circuit_type', 'circuit_type'); 
        $this->grocery_crud->display_as('circuit_type_id', 'Default port circuit type');
        $this->grocery_crud->set_relation('media_type_id', 'media_type', 'media_type'); 
        $this->grocery_crud->display_as('media_type_id', 'Default port media type');
        $this->grocery_crud->set_relation('connector_type_id', 'connector_type', 'connector_type'); 
        $this->grocery_crud->display_as('connector_type_id', 'Default port connector type');        
        $this->grocery_crud->display_as('manufacturer_id', 'Manufacturer');
        $this->grocery_crud->set_relation('manufacturer_id', 'manufacturer', 'manufacturer_name');  
        $this->grocery_crud->display_as('platform_id', 'Platform');
        $this->grocery_crud->set_relation('platform_id', 'platform', 'platform');          
        $this->grocery_crud->required_fields('slot_type', 'number_of_sub_slots', 'number_of_ports', 'number_of_ports_wide', 'number_of_ports_tall', 'circuit_type_id', 'media_type_id', 'connector_type_id', 'manufacturer_id', 'platform_id');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }     
    
    public function sub_slot(){
        $this->grocery_crud->set_table('sub_slot');
        $this->grocery_crud->set_subject('Sub Slot');   
        $this->grocery_crud->fields('slot_id', 'sub_slot_type_id', 'sub_slot_number', 'serial_number', 'notes');
        $id = $this->get_one_level_up_relation_id('sub_slot');
        $this->grocery_crud->display_as('slot_id', 'Slot');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('slot_id', 'slot', 'slot_number', array('slot_id' => $id)); 
        }else{
            $this->grocery_crud->set_relation('slot_id', 'slot', 'slot_number'); 
        }         
        $this->grocery_crud->display_as('sub_slot_type_id', 'Sub Slot Type');
        $this->grocery_crud->set_relation('sub_slot_type_id', 'sub_slot_type', 'sub_slot_type');  
        $this->grocery_crud->set_rules('slot_id', 'slot', 'callback_check_slot_number_of_open_sub_slots');        
        $this->grocery_crud->set_rules('sub_slot_type_id', 'sub_slot_type','callback_check_shelf_platform');    
        $this->grocery_crud->required_fields('sub_slot_type_id', 'slot_id', 'sub_slot_number');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    } 
    
    function check_slot_number_of_open_sub_slots() {
        $this->load->model('inv_model');
        $open_sub_slots = $this->inv_model->get_number_of_open_sub_slots_of_slot($_POST['slot_id']);
        if ($open_sub_slots < 1) {
            $this->form_validation->set_message('check_slot_number_of_open_sub_slots', 'Slot does not have any open sub slots remaining');
            return FALSE;
        }
    }    
    
    public function sub_slot_type(){
        $this->grocery_crud->set_table('sub_slot_type');
        $this->grocery_crud->set_subject('Sub Slot Type');  
        
        $this->grocery_crud->change_field_type('sub_slot_orientation', 'enum', array('Vertical','Horizontal'));
        $this->grocery_crud->fields('sub_slot_type', 'sub_slot_orientation', 'number_of_ports', 'number_of_ports_wide', 'number_of_ports_tall', 'port_numbering', 'manufacturer_id', 'platform_id', 'description');
        $this->grocery_crud->change_field_type('port_numbering', 'enum', array('Horizontal','Vertical'));    
//        $this->grocery_crud->set_relation('circuit_type_id', 'circuit_type', 'circuit_type'); 
//        $this->grocery_crud->display_as('circuit_type_id', 'Default port circuit type');
//        $this->grocery_crud->set_relation('media_type_id', 'media_type', 'media_type'); 
//        $this->grocery_crud->display_as('media_type_id', 'Default port media type');
//        $this->grocery_crud->set_relation('connector_type_id', 'connector_type', 'connector_type'); 
//        $this->grocery_crud->display_as('connector_type_id', 'Default port connector type');       
        $this->grocery_crud->display_as('manufacturer_id', 'Manufacturer');
        $this->grocery_crud->set_relation('manufacturer_id', 'manufacturer', 'manufacturer_name');    
        $this->grocery_crud->display_as('platform_id', 'Platform');
        $this->grocery_crud->set_relation('platform_id', 'platform', 'platform');          
        $this->grocery_crud->required_fields('sub_slot_type', 'number_of_ports', 'number_of_ports_wide', 'number_of_ports_tall', 'manufacturer_id', 'platform_id');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    } 

    public function port(){
        $this->grocery_crud->set_table('port');
        $this->grocery_crud->set_subject('Port');  
        
        $id = $this->get_one_level_up_relation_id('port');
        $this->grocery_crud->display_as('sub_slot_id', 'Sub slot');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
            $this->grocery_crud->set_relation('sub_slot_id', 'sub_slot', 'sub_slot_number', array('sub_slot_id' => $id));         
        }else{
            $this->grocery_crud->set_relation('sub_slot_id', 'sub_slot', 'sub_slot_number');         
        }       
        $this->grocery_crud->set_relation('circuit_type_id', 'circuit_type', 'circuit_type');  
        $this->grocery_crud->display_as('circuit_type_id', 'Circuit type');        
        $this->grocery_crud->set_relation('media_type_id', 'media_type', 'media_type'); 
        $this->grocery_crud->display_as('media_type_id', 'Media type');
        $this->grocery_crud->set_relation('connector_type_id', 'connector_type', 'connector_type');  
        $this->grocery_crud->display_as('connector_type_id', 'Connector type');   
        $this->grocery_crud->callback_after_insert(array($this, 'create_sub_ports'));
        $this->grocery_crud->callback_before_delete(array($this, 'delete_connection_from_port'));  
        $this->grocery_crud->set_rules('sub_slot_id', 'sub_slot', 'callback_check_sub_slot_number_of_open_ports');    
        $this->grocery_crud->required_fields('port_number', 'sub_slot_id', 'circuit_type_id', 'media_type_id', 'connector_type_id');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }   
    
    function delete_connection_from_port($primary_key){
        $this->load->model('inv_model');
        $this->inv_model->delete_connection_from_port($primary_key);
    }
    
    function check_sub_slot_number_of_open_ports() {
        $this->load->model('inv_model');
        $open_ports = $this->inv_model->get_number_of_open_ports_of_sub_slot($_POST['sub_slot_id']);
        if ($open_ports < 1) {
            $this->form_validation->set_message('check_sub_slot_number_of_open_ports', 'Slot / Sub Slot already has maximum allowed ports created');
            return FALSE;
        }
    }     
    
    function create_sub_ports($post_data, $insert_primary_key){
        $state_info = $this->grocery_crud->getStateInfo();
        if(isset($state_info->primary_key)){
            $primary_key = $state_info->primary_key;
        }else{
            $primary_key = $insert_primary_key;
        }
            $this->load->model('inv_model');
            $this->inv_model->create_sub_ports_for_new_port($primary_key);
    }    
    
    public function sub_port(){
        $this->grocery_crud->set_table('sub_port');
        $this->grocery_crud->set_subject('Sub Port');  
        
        if(end($_SESSION['type']) != 'connection'){
            $id = $this->get_one_level_up_relation_id('sub_port');
        }
        $this->grocery_crud->display_as('port_id', 'Port');
        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only current port
            $this->grocery_crud->set_relation('port_id', 'port', 'port_number', array('port_id' => $id));         
        }else{
            $this->grocery_crud->set_relation('port_id', 'port', 'port_number');         
        }        
        $this->grocery_crud->change_field_type('sub_port_type', 'enum', array('Transmit', 'Receive'));  
        $this->grocery_crud->display_as('connection_id', 'Connection ID');
        $this->grocery_crud->set_relation('connection_id', 'connection', 'connection_id');   
        
//        $this->grocery_crud->callback_add_field('connection_id', array($this,'_new_connection_id'));
//        $this->grocery_crud->callback_edit_field('connection_id', array($this,'_new_connection_id'));   

        $this->grocery_crud->set_rules('port_id', 'port','required|callback_port_unique_sub_port_type_check');
        $this->grocery_crud->set_rules('connection_id', 'connection','callback_connection_unique_sub_port_type_check');
        $this->grocery_crud->required_fields('sub_port_type', 'port_id'); 
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    
    
    function port_unique_sub_port_type_check(){ //check that sub port with specified type (transmit or receive) has not already been defined
        //first check if is LCX port and if it already has a subport of any type assigned
        //get chassis type to check if LCX panel or not
       
        $pos = stripos($slot_type, 'LCX');
        if ($pos === false) { //if slot is LCX panel do not create subports, if not, create normal ports with both transmit & receive sub ports                                     
            //create Transmit sub_port
            $this->db->set('port_id', $primary_key);
            $this->db->set('sub_port_type', 'Transmit');
            $this->db->insert('sub_port');
            //create Receive sub_port
            $this->db->set('port_id', $primary_key);
            $this->db->set('sub_port_type', 'Receive');
            $this->db->insert('sub_port');
        }
    }        
        //pull all records with circuit id and termination type submitted
        //if any records and add action, display error or if edit action and any record pulled is matching record other than current, display error
        $this->db->select('sub_port_id');
        $this->db->where('port_id', $_POST['port_id']);
        $this->db->where('sub_port_type', $_POST['sub_port_type']);
        $q = $this->db->get('sub_port');
        $state_info = $this->grocery_crud->getStateInfo();
        $state = $this->grocery_crud->getState();
        if ($q->num_rows() > 0 && $state == 'insert_validation') {
            $this->form_validation->set_message('port_unique_sub_port_type_check', 'The selected port with this sub port type has already been defined');
            return FALSE;            
        }elseif($q->num_rows() > 0 && $state == 'update_validation') {    
            foreach($q->result() as $row){
                if($row->sub_port_id != $state_info->primary_key){ //check all records and display error if any exist with same circuit & termination type that aren't current record
                    $this->form_validation->set_message('port_unique_sub_port_type_check', 'The selected port with this sub port type has already been defined');
                    return FALSE;
                }
            }
        }else{
            return TRUE;
        }
    }    
        
    function connection_unique_sub_port_type_check(){ //check that connection with specified type (transmit or receive) has not already been defined
        $this->db->select('sub_port_id');
        $this->db->where('connection_id', $_POST['connection_id']);
        $this->db->where('sub_port_type', $_POST['sub_port_type']);
        $q = $this->db->get('sub_port');
        $state_info = $this->grocery_crud->getStateInfo();
        $state = $this->grocery_crud->getState();
        if ($q->num_rows() > 0 && $state == 'insert_validation') {
            $this->form_validation->set_message('connection_unique_sub_port_type_check', 'The selected connection with this sub port type has already been defined');
            return FALSE;            
        }elseif($q->num_rows() > 0 && $state == 'update_validation') {    
            foreach($q->result() as $row){
                if($row->sub_port_id != $state_info->primary_key){ //check all records and display error if any exist with same circuit & termination type that aren't current record
                    $this->form_validation->set_message('connection_unique_sub_port_type_check', 'The selected connection with this sub port type has already been defined');
                    return FALSE;
                }
            }
        }else{
            return TRUE;
        }        
    }  
    
    public function connector_type(){
        $this->grocery_crud->set_table('connector_type');
        $this->grocery_crud->set_subject('Connector Type');   
        $this->grocery_crud->required_fields('connector_type');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }   
    
    public function circuit_type(){
        $this->grocery_crud->set_table('circuit_type');
        $this->grocery_crud->set_subject('Circuit Type'); 
        $this->grocery_crud->required_fields('circuit_type');    
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }      
    
    public function media_type(){
        $this->grocery_crud->set_table('media_type');
        $this->grocery_crud->set_subject('Media Type');  
        $this->grocery_crud->required_fields('media_type');   
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }      
    
    public function manufacturer(){
        $this->grocery_crud->set_table('manufacturer');
        $this->grocery_crud->set_subject('Manufacturer');  
        $this->grocery_crud->required_fields('manufacturer_name');    
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }        
    
    public function platform(){
        $this->grocery_crud->set_table('platform');
        $this->grocery_crud->set_subject('Platform');  
        $this->grocery_crud->required_fields('platform');    
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }        

    public function connection(){
        $this->grocery_crud->set_table('connection');
        $this->grocery_crud->set_subject('Connection');  
        $this->grocery_crud->callback_add_field('connection_id', array($this,'_autopopulate_new_connection_id'));
        $this->grocery_crud->callback_edit_field('connection_id', array($this,'_autopopulate_existing_connection_id'));        
        $this->grocery_crud->columns('connection_id', 'circuit');
        $this->grocery_crud->fields('connection_id', 'transmit_port_id', 'receive_port_id', 'circuit');
        $this->grocery_crud->set_relation_n_n('circuit','circuit_connection','circuit','connection_id','circuit_id','peerless_order_id');
        $this->grocery_crud->callback_add_field('transmit_port_id', array($this,'_add_populate_empty_transmit_ports'));
        $this->grocery_crud->callback_edit_field('transmit_port_id', array($this,'_edit_populate_empty_transmit_ports'));   
        $this->grocery_crud->callback_add_field('receive_port_id', array($this,'_add_populate_empty_receive_ports'));
        $this->grocery_crud->callback_edit_field('receive_port_id', array($this,'_edit_populate_empty_receive_ports')); 
        $this->grocery_crud->callback_after_insert(array($this, 'update_sub_port_connection'));
        $this->grocery_crud->callback_after_update(array($this, 'update_sub_port_connection'));
        $this->grocery_crud->required_fields('connection_id', 'transmit_port_id', 'receive_port_id');//, 'transmit_port', 'recieve_port');
        $this->grocery_crud->callback_before_insert(array($this, 'remove_sub_ports_from_post_data'));
        $this->grocery_crud->callback_before_update(array($this, 'remove_sub_ports_from_post_data'));
        $this->grocery_crud->set_rules('circuit', 'circuit','callback_restrict_connection_to_one_circuit');        
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    
    
    function restrict_connection_to_one_circuit(){ //check that only one circuit is selected for connection
        if (sizeof($_POST['circuit']) > 1) {
            $this->form_validation->set_message('restrict_connection_to_one_circuit', 'Connection can only be assigned to one Circuit');
            return FALSE;            
        }        
    }      
    
    function remove_sub_ports_from_post_data($post_array){
        unset($post_array['receive_port_id']);
        unset($post_array['transmit_port_id']);
        return $post_array;
    }
    
    function _add_populate_empty_transmit_ports(){
        $dropdown = '<div class="form-input-box" id="transmit_port_id_input_box">' . 
            '<select id="field-transmit_port_id"  name="transmit_port_id" class="chosen-select" data-placeholder="Select Open Transmit Port" style="width:auto"><option value=""></option>';        

        $this->load->model('inv_model');
        if(sizeof($_SESSION['list']) == 11 && isset($_SESSION['idlist'][10]) && isset($_SESSION['list'][10]) && $_SESSION['list'][10] == 'Transmit'){ //if session has port id set and is at sub port level
            $data = $this->inv_model->get_available_ports('Transmit', $_SESSION['idlist'][10]);        
        }else{
            $data = $this->inv_model->get_available_ports('Transmit');  
        }
    	
        if(isset($data)){
            for($i=0;$i<sizeof($data['hierarchy']);$i++){
                $dropdown .= '<option value="' . $data['unused_ports'][$i] . '">' . $data['hierarchy'][$i] . '</option>';                    
            }
        }
        $dropdown .= '</select></div>' . '<div class="clear"></div>';
        //sort the array alphabetically by hierarchy, but keep id values
    	//asort ($relation_array);        
    	return $dropdown;       

    }
    
    function _edit_populate_empty_transmit_ports(){
        $dropdown = '<div class="form-input-box" id="transmit_port_id_input_box">' . 
            '<select id="field-transmit_port_id"  name="transmit_port_id" class="chosen-select" data-placeholder="Select Open Transmit Port" style="width:auto">';        

        $this->load->model('inv_model');
        //get current transmit port hierarchy to be displayed as selected option in dropdown
        $selected_hierarchy = '';
        $state_info = $this->grocery_crud->getStateInfo();
        $port_id = $this->inv_model->get_one_port_from_connection($state_info->primary_key, 'Transmit'); //get transmit port for current connection
        $temp = $this->inv_model->get_hierarchy('port', $port_id);
                for($j=3;$j<sizeof($temp['list']); $j++){ //populate returned hierarchy into $selected_hierarchy
                    if($j == 3){
                        $selected_hierarchy = $selected_hierarchy . $temp['list'][$j];
                    }else{
                        if($temp['list'][$j] != 'Sub Slot 0'){
                            $selected_hierarchy = $selected_hierarchy . ' ► ' . $temp['list'][$j];
                        }
                    }
                }        
        $dropdown .= '<option value="' . $port_id . '" selected="selected">' . $selected_hierarchy . '</option><option value=""></option>';
        $data = $this->inv_model->get_available_ports('Transmit');        
        if(isset($data)){
            for($i=0;$i<sizeof($data['hierarchy']);$i++){
                $dropdown .= '<option value="' . $data['unused_ports'][$i] . '">' . $data['hierarchy'][$i] . '</option>';                    
            }
        }
        $dropdown .= '</select></div>' . '<div class="clear"></div>';
        //sort the array alphabetically by hierarchy, but keep id values
    	//asort ($relation_array);        
    	return $dropdown;       
    }    
    
    function _add_populate_empty_receive_ports(){
        $dropdown = '<div class="form-input-box" id="receive_port_id_input_box">' . 
            '<select id="field-receive_port_id"  name="receive_port_id" class="chosen-select" data-placeholder="Select Open Receive Port" style="width:auto"><option value=""></option>';        

        $this->load->model('inv_model');
        if(sizeof($_SESSION['list']) == 11 && isset($_SESSION['idlist'][10]) && isset($_SESSION['list'][10]) && $_SESSION['list'][10] == 'Receive'){ //if session has port id set and is at sub port level
            $data = $this->inv_model->get_available_ports('Receive', $_SESSION['idlist'][10]);        
        }else{
            $data = $this->inv_model->get_available_ports('Receive');  
        }           
    	
        if(isset($data)){
            for($i=0;$i<sizeof($data['hierarchy']);$i++){
                $dropdown .= '<option value="' . $data['unused_ports'][$i] . '">' . $data['hierarchy'][$i] . '</option>';                    
            }
        }
        $dropdown .= '</select></div>' . '<div class="clear"></div>';
        //sort the array alphabetically by hierarchy, but keep id values
    	//asort ($relation_array);        
    	return $dropdown;       

    }    
    
    function _edit_populate_empty_receive_ports(){
        $dropdown = '<div class="form-input-box" id="receive_port_id_input_box">' . 
            '<select id="field-receive_port_id"  name="receive_port_id" class="chosen-select" data-placeholder="Select Open Receive Port" style="width:auto">';        

        $this->load->model('inv_model');
        //get current transmit port hierarchy
        $selected_hierarchy = '';
        $state_info = $this->grocery_crud->getStateInfo();
        $port_id = $this->inv_model->get_one_port_from_connection($state_info->primary_key, 'Receive');
        $temp = $this->inv_model->get_hierarchy('port', $port_id);
                for($j=3;$j<sizeof($temp['list']); $j++){
                    if($j == 3){
                        $selected_hierarchy = $selected_hierarchy . $temp['list'][$j];
                    }else{
                        if($temp['list'][$j] != 'Sub Slot 0'){
                            $selected_hierarchy = $selected_hierarchy . ' ► ' . $temp['list'][$j];
                        }
                    }
                }        

        $dropdown .= '<option value="'. $port_id . '" selected="selected">' . $selected_hierarchy . '</option><option value=""></option>';
        $data = $this->inv_model->get_available_ports('Receive');        
        if(isset($data)){
            for($i=0;$i<sizeof($data['hierarchy']);$i++){
                $dropdown .= '<option value="' . $data['unused_ports'][$i] . '">' . $data['hierarchy'][$i] . '</option>';                    
            }
        }
        $dropdown .= '</select></div>' . '<div class="clear"></div>';
        //sort the array alphabetically by hierarchy, but keep id values
    	//asort ($relation_array);        
    	return $dropdown;        

    }    
    
    function update_sub_port_connection(){
        if(isset($_POST['transmit_port_id']) && $_POST['receive_port_id']){
            $this->load->model('inv_model');
            $this->inv_model->update_sub_ports_for_connection();
        }else{
            show_error('Both transmit & receive ports must be assigned for a valid connection');
        }
    }
    
//    public function connection(){
//        $this->grocery_crud->set_table('connection');
//        $this->grocery_crud->set_subject('Connection');  
//        //$this->grocery_crud->set_relation_n_n('Ports', 'port', 'port', 'cable_id', 'port_id', 'port_number');
//        //$this->grocery_crud->set_relation_n_n('External_Entities', 'cable_external_relation', 'external_entity', 'cable_id', 'external_entity_id', 'external_entity_name');
//        
//        //$this->grocery_crud->callback_after_insert(array($this, '_update_external_ids'));
//        //this->grocery_crud->callback_after_update(array($this, '_update_external_ids'));
//        
//        //$this->grocery_crud->callback_after_insert(array($this, '_update_blank_order_id_after_insert'));
//        //$this->grocery_crud->callback_after_update(array($this, '_update_blank_order_id_after_insert'));
//        
//        //$this->grocery_crud->callback_add_field('cable_order_id', array($this,'_insert_cable_order_id'));
//        //$this->grocery_crud->callback_edit_field('cable_order_id', array($this,'_insert_cable_order_id'));
//        
////        $this->grocery_crud->add_fields('external_id');
////        $this->grocery_crud->callback_add_field('external_id', array($this,'_insert_external_id'));
//        
//        
////        if(isset($id)){ //if browsing within hierarchy, limit dropdown to only within current hierarchy
////            $this->grocery_crud->set_relation('port_id', 'port', 'port_number', array('sub_slot_id' => $id)); 
////        }else{
////            $this->grocery_crud->set_relation('port_id', 'port', 'port_number');             
////        }
////        $this->grocery_crud->display_as('transmit_port_id', 'Transmit Connection');
////        $this->grocery_crud->set_relation('port_id', 'port', 'port_number'); 
//        $this->grocery_crud->callback_add_field('connection_id', array($this,'_autopopulate_new_connection_id'));
//        $this->grocery_crud->callback_edit_field('connection_id', array($this,'_autopopulate_existing_connection_id'));        
//        $this->grocery_crud->columns('connection_id', 'circuit_id');
//        $this->grocery_crud->fields('connection_id', 'circuit_id');
//        $this->grocery_crud->set_relation('circuit_id', 'circuit', 'peerless_order_id'); 
//
//        $output = $this->grocery_crud->render();
//        
//        $this->create_crud_view($output);
//    }

    //get value of next auto increment connection id and insert it into form as readonly for add form   
    function _autopopulate_new_connection_id(){ 
        $result = mysql_query("SHOW TABLE STATUS LIKE 'connection'");
        $row = mysql_fetch_array($result);
        $nextId = $row['Auto_increment'];
        mysql_free_result($result);
        return '<input id="field-connection_id" name="connection_id" type="text" value="' . $nextId . '" class="numeric" readonly="readonly" maxlength="11"> (Connection ID is automatically generated)';
    }
    //show current connection id that was created automatically and display as read only for edit form
    function _autopopulate_existing_connection_id($primary_key){
        return '<input id="field-connection_id" name="connection_id" type="text" value="' . $primary_key . '" class="numeric" readonly="readonly" maxlength="11"> (Connection ID is automatically generated)';
    }       

    public function connection_external_relation(){
        $this->grocery_crud->set_table('connection_external_relation');
        $this->grocery_crud->set_subject('Connection External Relation');        
        $this->grocery_crud->display_as('connection_id', 'Connection ID');
        if(end($_SESSION['type']) == 'connection'){ //if coming from connection screen, limit sub slot dropdown to current place in hierarchy
            $this->grocery_crud->set_relation('connection_id', 'connection', 'connection_id', array('connection_id' => end($_SESSION['idlist'])));        
        }else{
            $this->grocery_crud->set_relation('connection_id', 'connection', 'connection_id');      
        }        
        $this->grocery_crud->display_as('external_entity_id', 'External Entity Name');
        $this->grocery_crud->set_relation('external_entity_id', 'external_entity', 'external_entity_name');  
        $this->grocery_crud->required_fields('external_entity_id', 'connection_id');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }  
    
    public function external_entity(){
        $this->grocery_crud->set_table('external_entity');
        $this->grocery_crud->set_subject('External Entity'); 
        $this->grocery_crud->required_fields('external_entity_name');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }    
    
    public function circuit_connection(){
        $this->grocery_crud->set_table('circuit_connection');
        $this->grocery_crud->set_subject('Circuit Connection Relation');        
        $this->grocery_crud->display_as('circuit_id', 'Circuit');
        $this->grocery_crud->set_relation('connection_id', 'connection', 'connection_id');            
        $this->grocery_crud->display_as('connection_id', 'Connection');
        $this->grocery_crud->set_relation('circuit_id', 'circuit', 'peerless_order_id');  
        $this->grocery_crud->required_fields('connection_id', 'circuit_id');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
            
    }
    
    public function circuit($operation = null){

        $this->grocery_crud->set_model('Circuit_model');
        
        $this->grocery_crud->set_table('circuit');
        $this->grocery_crud->set_subject('Circuit'); 
        if( $operation == 'insert_validation' || $operation == 'insert'){
            $this->grocery_crud->set_rules('peerless_order_id', 'Peerless Order ID', 'trim|required|is_unique[circuit.peerless_order_id]'); 
        }else{
            $this->grocery_crud->set_rules('peerless_order_id', 'Peerless Order ID', 'trim|required'); 
        }
        $this->grocery_crud->columns('peerless_order_id');
        $this->grocery_crud->fields('peerless_order_id', 'a_end_shelf_id', 'z_end_shelf_id', 'connections');
        $this->grocery_crud->display_as('a_end_shelf_id', 'A-End shelf');
        $this->grocery_crud->display_as('z_end_shelf_id', 'Z-End shelf');
        $this->grocery_crud->set_relation_n_n('connections','circuit_connection','connection','circuit_id','connection_id','connection_id');
        $this->grocery_crud->callback_add_field('a_end_shelf_id', array($this,'_add_populate_a_end_shelves'));
        $this->grocery_crud->callback_edit_field('a_end_shelf_id', array($this,'_edit_populate_a_end_shelves'));   
        $this->grocery_crud->callback_add_field('z_end_shelf_id', array($this,'_add_populate_z_end_shelves'));
        $this->grocery_crud->callback_edit_field('z_end_shelf_id', array($this,'_edit_populate_z_end_shelves')); 
        $this->grocery_crud->callback_after_insert(array($this, 'update_circuit_shelves'));
        $this->grocery_crud->callback_after_update(array($this, 'update_circuit_shelves'));
        $this->grocery_crud->callback_before_insert(array($this, 'remove_shelves_from_post_data'));
        $this->grocery_crud->callback_before_update(array($this, 'remove_shelves_from_post_data'));
        $this->grocery_crud->set_rules('a_end_shelf_id', 'A-End shelf', 'required|callback_shelf_terminating_check');
        $this->grocery_crud->set_rules('connections', 'connections', 'callback_validate_connections|callback_check_connections_for_existing_circuits');
        
        //$this->grocery_crud->set_rules('z_end_shelf_id', 'z_end_shelf','callback_shelf_terminating_check');
        $this->grocery_crud->required_fields('peerless_order_id', 'a_end_shelf_id', 'z_end_shelf_id');
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);        
    }    
    
    function shelf_terminating_check(){ //check that both a & z end shelves are terminating type
        $this->db->select('shelf_id');
        $this->db->join('chassis_type', 'shelf.chassis_type_id = chassis_type.chassis_type_id');
        $this->db->where('shelf_id', $_POST['a_end_shelf_id']); 
        $this->db->where('terminating', 'Non-Terminating');
        $q = $this->db->get('shelf');
        $this->db->select('shelf_id');
        $this->db->join('chassis_type', 'shelf.chassis_type_id = chassis_type.chassis_type_id');
        $this->db->where('shelf_id', $_POST['z_end_shelf_id']); 
        $this->db->where('terminating', 'Non-Terminating');
        $q2 = $this->db->get('shelf');        
        $state = $this->grocery_crud->getState();
        if($q->num_rows() > 0 && $q2->num_rows() > 0 && ($state == 'insert_validation' || $state == 'update_validation')){
            $this->form_validation->set_message('shelf_terminating_check', 'The A-End & Z-End Shelves must both be a Terminating Chassis Type');
            return FALSE;             
        }elseif ($q->num_rows() > 0 && ($state == 'insert_validation' || $state == 'update_validation')) {
            $this->form_validation->set_message('shelf_terminating_check', 'The A-End Shelf must be a Terminating Chassis Type');
            return FALSE;            
        }elseif ($q2->num_rows() > 0 && ($state == 'insert_validation' || $state == 'update_validation')) {
            $this->form_validation->set_message('shelf_terminating_check', 'The Z-End Shelf must be a Terminating Chassis Type');
            return FALSE;            
        }else{
            return TRUE;
        }        
    }     

    function remove_shelves_from_post_data($post_array){
        unset($post_array['a_end_shelf_id']);
        unset($post_array['z_end_shelf_id']);
        //unset($post_array['connections']);
        return $post_array;
    }    
    
    function _add_populate_a_end_shelves(){
        $dropdown = '<div class="form-input-box" id="a_end_shelf_id_input_box">' . 
            '<select id="field-a_end_shelf_id"  name="a_end_shelf_id" class="chosen-select" data-placeholder="Select A-End Shelf" style="width:auto"><option value=""></option>';        

        $this->load->model('inv_model');
        $data = $this->inv_model->get_all_terminating_shelves();        
    	
        if(isset($data)){
            for($i=0;$i<sizeof($data['hierarchy']);$i++){
                $dropdown .= '<option value="' . $data['shelves'][$i] . '">' . $data['hierarchy'][$i] . '</option>';                    
            }
        }
        $dropdown .= '</select></div>' . '<div class="clear"></div>';
        //sort the array alphabetically by hierarchy, but keep id values
    	//asort ($relation_array);        
    	return $dropdown;       
    }
    
    function _edit_populate_a_end_shelves(){
        $dropdown = '<div class="form-input-box" id="a_end_shelf_id_input_box">' . 
            '<select id="field-a_end_shelf_id"  name="a_end_shelf_id" class="chosen-select" data-placeholder="Select A End Shelf" style="width:auto">';        

        $this->load->model('inv_model');
        //get current A-End shelf hierarchy to be displayed as selected option in dropdown
        $selected_hierarchy = '';
        $state_info = $this->grocery_crud->getStateInfo();
        $shelf_id = $this->inv_model->get_one_shelf_from_circuit($state_info->primary_key, 'A-End'); //get A-End shelf for current circuit
        $temp = $this->inv_model->get_hierarchy('shelf', $shelf_id);
                for($j=3;$j<sizeof($temp['list']); $j++){ //populate returned hierarchy into $selected_hierarchy
                    if($j == 3){
                        $selected_hierarchy = $selected_hierarchy . $temp['list'][$j];
                    }else{
                        $selected_hierarchy = $selected_hierarchy . ' ► ' . $temp['list'][$j];
                    }
                }        
        $dropdown .= '<option value="' . $shelf_id . '" selected="selected">' . $selected_hierarchy . '</option><option value=""></option>';
        $data = $this->inv_model->get_all_terminating_shelves();        
        if(isset($data)){
            for($i=0;$i<sizeof($data['hierarchy']);$i++){
                $dropdown .= '<option value="' . $data['shelves'][$i] . '">' . $data['hierarchy'][$i] . '</option>';                    
            }
        }
        $dropdown .= '</select></div>' . '<div class="clear"></div>';
        //sort the array alphabetically by hierarchy, but keep id values
    	//asort ($relation_array);        
    	return $dropdown;       
    }    
    
    function _add_populate_z_end_shelves(){
        $dropdown = '<div class="form-input-box" id="z_end_shelf_id_input_box">' . 
            '<select id="field-z_end_shelf_id"  name="z_end_shelf_id" class="chosen-select" data-placeholder="Select Z End Shelf" style="width:auto"><option value=""></option>';        

        $this->load->model('inv_model');
        $data = $this->inv_model->get_all_terminating_shelves();        
    	
        if(isset($data)){
            for($i=0;$i<sizeof($data['hierarchy']);$i++){
                $dropdown .= '<option value="' . $data['shelves'][$i] . '">' . $data['hierarchy'][$i] . '</option>';                    
            }
        }
        $dropdown .= '</select></div>' . '<div class="clear"></div>';
        //sort the array alphabetically by hierarchy, but keep id values
    	//asort ($relation_array);        
    	return $dropdown;       
    }    
    
    function _edit_populate_z_end_shelves(){
        $dropdown = '<div class="form-input-box" id="z_end_shelf_id_input_box">' . 
            '<select id="field-z_end_shelf_id"  name="z_end_shelf_id" class="chosen-select" data-placeholder="Select Z End Shelf" style="width:auto">';        

        $this->load->model('inv_model');
        //get current Z-End shelf hierarchy to be displayed as selected option in dropdown
        $selected_hierarchy = '';
        $state_info = $this->grocery_crud->getStateInfo();
        $shelf_id = $this->inv_model->get_one_shelf_from_circuit($state_info->primary_key, 'Z-End'); //get A-End shelf for current circuit
        $temp = $this->inv_model->get_hierarchy('shelf', $shelf_id);
                for($j=3;$j<sizeof($temp['list']); $j++){
                    if($j == 3){
                        $selected_hierarchy = $selected_hierarchy . $temp['list'][$j];
                    }else{
                        $selected_hierarchy = $selected_hierarchy . ' ► ' . $temp['list'][$j];
                    }
                }        

        $dropdown .= '<option value="'. $shelf_id . '" selected="selected">' . $selected_hierarchy . '</option><option value=""></option>';
        $data = $this->inv_model->get_all_terminating_shelves();        
        if(isset($data)){
            for($i=0;$i<sizeof($data['hierarchy']);$i++){
                $dropdown .= '<option value="' . $data['shelves'][$i] . '">' . $data['hierarchy'][$i] . '</option>';                    
            }
        }
        $dropdown .= '</select></div>' . '<div class="clear"></div>';
        //sort the array alphabetically by hierarchy, but keep id values     
    	return $dropdown;        
    }     
    
    function update_circuit_shelves($post_data, $insert_primary_key){
        $state_info = $this->grocery_crud->getStateInfo();
        if(isset($state_info->primary_key)){
            $primary_key = $state_info->primary_key;
        }else{
            $primary_key = $insert_primary_key;
        }
        if(isset($_POST['a_end_shelf_id']) && $_POST['z_end_shelf_id']){
            $this->load->model('inv_model');
            $this->inv_model->update_shelves_for_circuit($primary_key);
        }else{
            show_error('Both A-End & Z-End shelves must be assigned for a valid circuit');
        }        
    }
    
    function validate_connections(){
        echo 'in validate_connections function';
        //check each connection in POST data connects to shelf or connection connected to shelf
        //check_connection_shelf($connection_id, $a_end_shelf_id, $z_end_shelf_id);
        //check if any connection goes to either shelf to start with
        //then check if and post connections connect to this connection
        //then if not, check if connect to shelf itself
    }
    
    function check_connections_for_existing_circuits(){
        if(isset($_POST['connections'])){
        $state_info = $this->grocery_crud->getStateInfo();
        if(isset($state_info->primary_key)){
            $primary_key = $state_info->primary_key;
        }else{
            $primary_key = $insert_primary_key;
        }            
            $this->load->model('inv_model');
            $duplicates = $this->inv_model->get_connections_with_assigned_circuit($_POST['connections'], $primary_key);
            $connections = null;
            if(isset($duplicates)){
                foreach($duplicates as $conn){
                    $connections = $connections . ($conn == end($duplicates) ? $conn : $conn . ', ');
                }
                $this->form_validation->set_message('check_connections_for_existing_circuits', 'Connections (' . $connections . ') have already been assigned to other circuits');
                return FALSE;                
            }
        }
    }
    
    public function circuit_shelf(){
        $this->grocery_crud->set_table('circuit_shelf');
        $this->grocery_crud->set_subject('Circuit Shelf Termination');
        $this->grocery_crud->change_field_type('termination_type', 'enum', array('A-End', 'Z-End'));  
        $this->grocery_crud->display_as('circuit_id', 'Circuit (Peerless Order ID)');
        if(end($_SESSION['type']) == 'circuit' && isset($_SESSION['idlist'])){
            $this->grocery_crud->set_relation('circuit_id', 'circuit', 'peerless_order_id', array('circuit_id' => end($_SESSION['idlist'])));
        }else{
            $this->grocery_crud->set_relation('circuit_id', 'circuit', 'peerless_order_id');   
        }
        $this->grocery_crud->display_as('shelf_id', 'Shelf');
        $this->grocery_crud->set_relation('shelf_id', 'shelf', 'shelf_name');
        $this->grocery_crud->set_rules('circuit_id', 'circuit','required|callback_circuit_unique_end_check');
        $this->grocery_crud->required_fields('circuit_id', 'shelf_id', 'termination_type');          
        $output = $this->grocery_crud->render();
        
        $this->create_crud_view($output);
    }
    
    function circuit_unique_end_check(){
        //pull all records with circuit id and termination type submitted
        //if any records and add action, display error or if edit action and any record pulled is matching record other than current, display error
        $this->db->select('circuit_shelf_id');
        $this->db->where('circuit_id', $_POST['circuit_id']);
        $this->db->where('termination_type', $_POST['termination_type']);
        $q = $this->db->get('circuit_shelf');
        $state_info = $this->grocery_crud->getStateInfo();
        $state = $this->grocery_crud->getState();
        if ($q->num_rows() > 0 && $state == 'insert_validation') {
            $this->form_validation->set_message('circuit_unique_end_check', 'Shelf with this termination type has already been defined for this circuit');
            return FALSE;            
        }elseif($q->num_rows() > 0 && $state == 'update_validation') {    
            foreach($q->result() as $row){
                if($row->circuit_shelf_id != $state_info->primary_key){ //check all records and display error if any exist with same circuit & termination type that aren't current record
                    $this->form_validation->set_message('circuit_unique_end_check', 'Shelf with this termination type has already been defined for this circuit');
                    return FALSE;
                }
            }
        }else{
            return TRUE;
        }
    }
    
    public function connection_popup(){
        $this->grocery_crud->set_table('connection');
        $this->grocery_crud->set_subject('Connection');  
        $this->grocery_crud->callback_add_field('connection_id', array($this,'_autopopulate_new_connection_id'));
        $this->grocery_crud->callback_edit_field('connection_id', array($this,'_autopopulate_existing_connection_id'));        
        $this->grocery_crud->columns('connection_id', 'circuit_id');
        $this->grocery_crud->fields('connection_id', 'circuit_id');
        $this->grocery_crud->set_relation('circuit_id', 'circuit', 'peerless_order_id'); 

        $output = $this->grocery_crud->render();
        $this->load->view('example', $output);
    }    
    
    function create_crud_view($output = null){
        $this->load->view('grocery_view', $output);
    }    
    
    public function edit(){
        $this->load->model('inv_model');
        $data['tables'] = $this->inv_model->get_tables();
        $_SESSION['idlist'] = null;
        $this->load->view('edit_tables', $data);
    }
    
    function add_user(){
        $this->load->view('add_user');
    }
    
    function create_user(){
        $this->load->library('form_validation');
        //field name, error message, validation rules
        $this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
        $this->form_validation->set_rules('email_address', 'Email Address', 'trim|required|valid_email|is_unique[user.email_address]');
    
        $this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[4]|is_unique[user.username]|callback__username_check');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|max_length[32]');
        $this->form_validation->set_rules('password2', 'Password Confirmation', 'trim|required|matches[password]');
        
        if($this->form_validation->run() == FALSE){
            $this->load->view('add_user');
        }else{
            $this->load->model('user_model');
            $query = $this->user_model->create_user();
            if($query){
                $data['username'] = $this->input->post('username');
                $this->load->view('add_user_successful', $data);
            }else{
                $this->load->view('add_user');
            }
            
        }
    }   
        function _username_check($username){
            print_r($username);
            if($username == 'Username'){
                $this->form_validation->set_message('_username_check', 'The %s field can not be the word "Username"');
                return FALSE;     
            }else{
                return TRUE;
            }
        }    
}

