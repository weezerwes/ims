<?php 

session_start();
//echo !isset($_SESSION['list']) . ' ';
if(!isset($_SESSION['list'])){
    $_SESSION['list'] = array();
    $_SESSION['type'] = array();
    //echo "array not set";
}

class Inv extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('grocery_CRUD');
        //$this->is_logged_in();
    }
    
    function is_logged_in($view){
        $logged_in = $this->session->userdata('is_logged_in');
        
        //views that allow editing data have _admin extension. 
        if(!isset($logged_in) || $logged_in == FALSE){
            return $view;
        }else{
            return $view . '_admin';
        }
    }

    function index() {
        $_SESSION['list'] = array(); //holds list of hierarchy choices to create breadcrumbs
        $_SESSION['previouslevelink'] = array(); //holds http links chosen to navigate back via breadcrumbs
        $_SESSION['counter'] = 0; //number of choices made - determines place in arrays
        $_SESSION['type'] = array(); //name of category being selected - corresponds to name of table
        $_SESSION['type'][0] = "country"; //sets first type to begin with country, the top of hierarchy
        $_SESSION['idlist'] = array();
        $this->load->model('inv_model'); 
        $data['country'] = $this->inv_model->get_country();
        $data['field_name'] = $_SESSION['type'][0] . "_name"; //name of field from table that will be displayed
        $data['extension'] = null;
        $this->load->view('inv_view', $data);
        //$this->load->view('inv_view', $data);
        
    }

    function menu() {

        if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["id"]) && isset($_GET["type"])) {
            //check type to see level of hierarchy chosen to this point and display next level
            switch ($_GET["type"]) {
                
                case "country": //country chosen, display city
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];
                    $_SESSION['type'][] = "city";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_name";
                    $data['city'] = $this->inv_model->get_city($_GET["id"]);
                    if (empty($data['city'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
                        $data['extension'] = null;
                        //add state to city string for display purposes
                        for($i=0;$i<sizeof($data['city']);$i++){
                            if($data['city'][$i]->state_name !== ''){
                                $data['city'][$i]->city_name = $data['city'][$i]->city_name . ', ' . $data['city'][$i]->state_name;
                            }
                        }
                        $this->load->view('inv_view', $data);
                    }
                    break;

                case "city": //city & country chosen, display building
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];
                    $_SESSION['type'][] = "building";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_address";
                    $data['building'] = $this->inv_model->get_building($_GET["id"]);
                    if (empty($data['building'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
//                        $data['extension'][0][0] = 'Zip Code';
//                        $data['extension'][0][1] = 'building_zip';
                        $this->load->view('inv_view', $data);
                    }
                    break;

                case "building": //building, city, & country chosen, display location
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "location";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_name";
                    $data['location'] = $this->inv_model->get_location($_GET["id"]);
                    if (empty($data['location'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
                        $data['extension'][0][0] = 'Suite';
                        $data['extension'][0][1] = 'suite_number';
                        $data['extension'][1][0] = 'Controlling Party';
                        $data['extension'][1][1] = 'external_entity_name';                         
                        $this->load->view('inv_view', $data);
                    }
                    break;

                case "location": //location, building, city, & country chosen, display aisles
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];
                    $_SESSION['type'][] = "aisle";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_number";
                    $data['aisle'] = $this->inv_model->get_aisle($_GET["id"]);
                    if (empty($data['aisle'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    }
                    break;
                    
                case "aisle": //aisle, location, building, city, & country chosen, display bay
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "bay";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_number";
                    $data['bay'] = $this->inv_model->get_bay($_GET["id"]);
                    if (empty($data['bay'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
                        //fill extension with 2 dimensional array holding property name & value
                        $data['extension'][0][0] = 'Bay Type';
                        $data['extension'][0][1] = 'bay_type';
                        $data['extension'][1][0] = 'Height';
                        $data['extension'][1][1] = 'bay_height';
                        $data['extension'][2][0] = 'Bay Nickname';
                        $data['extension'][2][1] = 'bay_nickname';                         
                        $this->load->view('inv_view', $data);
                    }
                    break;  
                    
                case "bay": //bay, aisle, location, building, city, & country chosen, display shelf
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "shelf";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_name";
                    $data['shelf'] = $this->inv_model->get_shelf($_GET["id"]);
                    if (empty($data['shelf'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
                        $data['extension'][0][0] = 'Shelf Name';
                        $data['extension'][0][1] = 'shelf_name';                          
                        $data['extension'][1][0] = 'Chassis Type';
                        $data['extension'][1][1] = 'chassis_type';                       
                        $data['extension'][2][0] = 'Number Of Slots';
                        $data['extension'][2][1] = 'number_of_slots';
                        $data['extension'][3][0] = 'Height';
                        $data['extension'][3][1] = 'height';
                        $data['extension'][4][0] = 'Top Rack Unit';
                        $data['extension'][4][1] = 'top_rack_unit'; 
                        $data['extension'][5][0] = 'Type';
                        $data['extension'][5][1] = 'terminating';                        
                        $data['extension'][6][0] = 'Manufacturer';
                        $data['extension'][6][1] = 'manufacturer_name';
                        $data['extension'][7][0] = 'Platform';
                        $data['extension'][7][1] = 'platform';                          
                        $data['extension'][8][0] = 'Part Number';
                        $data['extension'][8][1] = 'part_number';                        
                        $data['extension'][9][0] = 'Description';
                        $data['extension'][9][1] = 'description';
                        $data['extension'][10][0] = 'Serial Number';
                        $data['extension'][10][1] = 'serial_number';                         
                       
                        $this->load->view('inv_view', $data);
                    }
                    break;   
                    
                case "shelf": //shelf, bay, aisle, location, building, city, & country chosen, display slot
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "slot";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_number";
                    $data['slot'] = $this->inv_model->get_slot($_GET["id"]);
                    if (empty($data['slot'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
                        $data['extension'][0][0] = 'Slot Type';
                        $data['extension'][0][1] = 'slot_type';
                        $data['extension'][1][0] = 'Number Of Sub Slots';
                        $data['extension'][1][1] = 'number_of_sub_slots';                         
                        $data['extension'][2][0] = 'Number Of Ports';
                        $data['extension'][2][1] = 'number_of_ports'; 
                        $data['extension'][3][0] = 'Manufacturer';
                        $data['extension'][3][1] = 'manufacturer_name';
                        $data['extension'][4][0] = 'Platform';
                        $data['extension'][4][1] = 'platform';                        
                        $data['extension'][5][0] = 'Description';
                        $data['extension'][5][1] = 'description';
                        $data['extension'][6][0] = 'Serial Number';
                        $data['extension'][6][1] = 'serial_number';                         
                        $data['extension'][7][0] = 'Notes';
                        $data['extension'][7][1] = 'notes';                        
                        
                        $this->load->view('inv_view', $data);
                    }
                    break;  
                    
                case "slot": //slot, shelf, bay, aisle, location, building, city, & country chosen, display sub_slot
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "sub_slot";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_number";
                    $data['sub_slot'] = $this->inv_model->get_sub_slot($_GET["id"]);
                    if(!empty($data['sub_slot']) && $data['sub_slot'][0]->sub_slot_number == '0'){
                        $_GET['type'] = 'sub_slot';
                        $_GET['id'] = $data['sub_slot'][0]->sub_slot_id;
                        $this->menu();
                        break;
                    }
                    if (empty($data['sub_slot'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
                        $data['extension'][0][0] = 'Sub Slot Type';
                        $data['extension'][0][1] = 'sub_slot_type';
                        $data['extension'][1][0] = 'Number Of Ports';
                        $data['extension'][1][1] = 'number_of_ports';                        
                        $data['extension'][2][0] = 'Front Slot';
                        $data['extension'][2][1] = 'front_slot';
                        $data['extension'][3][0] = 'Manufacturer';
                        $data['extension'][3][1] = 'manufacturer_name';
                        $data['extension'][4][0] = 'Platform';
                        $data['extension'][4][1] = 'platform';                          
                        $data['extension'][5][0] = 'Description';
                        $data['extension'][5][1] = 'description';
                        $data['extension'][6][0] = 'Serial Number';
                        $data['extension'][6][1] = 'serial_number';                         
                        $data['extension'][7][0] = 'Notes';
                        $data['extension'][7][1] = 'notes';                        
                        $this->load->view('inv_view', $data);
                    }
                    break;
                    
                case "sub_slot": //sub_slot, slot, shelf, bay, aisle, location, building, city, & country chosen, display port
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "port";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_number";
                    $data['port'] = $this->inv_model->get_port($_GET["id"]);
                    if (empty($data['port'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
                        $data['extension'][0][0] = 'Circuit Type';
                        $data['extension'][0][1] = 'circuit_type';                        
                        $data['extension'][1][0] = 'Connector Type';
                        $data['extension'][1][1] = 'connector_type';
                        $data['extension'][2][0] = 'Media Type';
                        $data['extension'][2][1] = 'media_type';                        
                        $this->load->view('inv_view', $data);
                    }
                    break; 
                    
                case "port": //port, sub_slot, slot, shelf, bay, aisle, location, building, city, & country chosen, display sub_port
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "sub_port";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = end($_SESSION['type']) . "_type";
                    $data['sub_port'] = $this->inv_model->get_sub_port($_GET["id"]);
                    if (empty($data['sub_port'])) {
                        $data['extension'] = null;
                        $this->load->view('inv_view', $data);
                    } else {
//                        $data['extension'][0][0] = 'Circuit Type';
//                        $data['extension'][0][1] = 'circuit_type';                        
//                        $data['extension'][1][0] = 'Connector Type';
//                        $data['extension'][1][1] = 'connector_type';
//                        $data['extension'][2][0] = 'Media Type';
//                        $data['extension'][2][1] = 'media_type';                        
                        $this->load->view('inv_view', $data);
                    }
                    break;                      
                    
                case "sub_port": //sub_port, port, sub_slot, slot, shelf, bay, aisle, location, building, city, & country chosen, display connection
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "connection";
                    $_SESSION['idlist'] = $temp['idlist'];                    
                    $temp = $this->inv_model->get_connection($_GET["id"]);
                    if(isset($temp['connection'][0]->connection_id)){ //if connection exists, get connection id
                        $_GET["id"] = $temp['connection'][0]->connection_id;
                        $_GET["type"] = 'connection';
                    }else{ //else no connection exists, reset connection data to null and call normal view and break out
                        $_GET["id"] = null;
                        $data = null;
                        $data['connection'] = array();
                        $_SESSION['type'][] = 'connection';
                        $this->load->view('inv_view', $data);
                        break; 
                    }
                    if(isset($temp['connection'][0]->port_id)){
                        $_GET['port'] = $temp['connection'][0]->port_id;
                    }else{
                        $_GET['port'] = null;
                    }
                    //if connection exists, fall through to treat as connection 
        
//                    $this->load->model('inv_model');
//                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
//                    $_SESSION['list'] = $temp['list'];
//                    $_SESSION['previouslevelink'] = $temp['links'];
//                    $_SESSION['type'] = $temp['type'];                    
//                    $_SESSION['type'][] = "connection";
//                    $_SESSION['idlist'] = $temp['idlist'];
//                    $data['field_name'] = "connection_id";
//                    $temp = $this->inv_model->get_connection($_GET["id"]);
//                    $data['connection'] = $temp['connection'];
//                    if(isset($temp['ext'])){ //if external entity data present
//                        $data['extension_values'] = $temp['ext'];
//                    }else{
//                        $data['extension_values'] = null;                        
//                    }
//                    if (empty($data['connection'])) {
//                        $data['extension'] = null;
//                        $this->load->view('inv_view', $data);
//                    } else {
//                        $data['extension'][0][0] = 'External Entity';
//                        $data['extension'][0][1] = 'external_entity_name';
//                        $data['extension'][0][2] = 'External Order ID';
//                        $data['extension'][0][3] = 'external_order_id';
//                        $data['extension'][0][4] = 'External Connection ID';
//                        $data['extension'][0][5] = 'external_connection_id';
//                        $data['extension'][0][6] = 'External Ticket ID';
//                        $data['extension'][0][7] = 'external_ticket_id';                        
//                        $this->load->view('inv_view', $data);
//                    }
//                    break;     
//                    
                case "connection": //connection, sub_port, port, sub_slot, slot, shelf, bay, aisle, location, building, city, & country chosen, display connection details
                    $this->load->model('inv_model');
                    if(isset($_GET['port'])){
                        $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"], $_GET['port']);
                    }else{
                        $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    }
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    $_SESSION['type'][] = "connection";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = "connection_id";
                    $data['connection_id'] = $_GET["id"];
                    $temp = $this->inv_model->get_full_connection($_GET["id"]);
                    $data['sub_port'] = $temp['sub_port'];
                    $data['circuit_id'] = $temp['circuit_id'];
                    $data['peerless_order_id'] = $temp['peerless_order_id'];
                    $data['circuit_connection_id'] = $temp['circuit_connection_id'];
                    $data['transmit_hierarchy'] = null;
                    $data['transmit_links'] = null;
                    $data['receive_hierarchy'] = null;
                    $data['receive_links'] = null;
                    for($i=0;$i<sizeof($data['sub_port']);$i++){
                        if(isset($data['sub_port'][$i]->sub_port_type) && $data['sub_port'][$i]->sub_port_type == 'Transmit'){
                            $temp_hierarchy = $this->inv_model->get_hierarchy('sub_port', $data['sub_port'][$i]->sub_port_id);
                            $data['transmit_hierarchy'] = $temp_hierarchy['list'];
                            $data['transmit_links'] = $temp_hierarchy['links'];;
                        }else if(isset($data['sub_port'][$i]->sub_port_type) && $data['sub_port'][$i]->sub_port_type == 'Receive'){
                            $temp_hierarchy = $this->inv_model->get_hierarchy('sub_port', $data['sub_port'][$i]->sub_port_id);
                            $data['receive_hierarchy'] = $temp_hierarchy['list'];
                            $data['receive_links'] = $temp_hierarchy['links'];;    
                        }   
                    }

                    if(isset($temp['ext'])){ //if external entity data present
                        $data['extension_values'] = $temp['ext'];
                    }else{
                        $data['extension_values'] = null;                        
                    }
                    if (empty($data['sub_port'])) {
                        $data['extension'] = null;
                        $this->load->view('connection_view', $data);
                    } else {
                        $data['extension'][0][0] = 'External Entity';
                        $data['extension'][0][1] = 'external_entity_name';
                        $data['extension'][0][2] = 'External Order ID';
                        $data['extension'][0][3] = 'external_order_id';
                        $data['extension'][0][4] = 'External Connection ID';
                        $data['extension'][0][5] = 'external_connection_id';
                        $data['extension'][0][6] = 'External Ticket ID';
                        $data['extension'][0][7] = 'external_ticket_id';                        
                        $this->load->view('connection_view', $data);
                    }
                    break;                    

//                case "connection": //port, sub_slot, slot, shelf, bay, aisle, location, building, city, & country chosen, display slot
//                    $this->load->model('inv_model');
//                    //pass port_id to get_hierarchy method to pull correct hierarchy of the multiple possibilities connection can have
//                    if(isset($_GET['port'])){
//                        $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"], $_GET['port']);
//                    }else{
//                        $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
//                    }
//                    $_SESSION['list'] = $temp['list'];
//                    $_SESSION['previouslevelink'] = $temp['links'];
//                    $_SESSION['type'] = $temp['type'];                    
//                    $_SESSION['type'][] = "port";
//                    $_SESSION['idlist'] = $temp['idlist'];
//                    $data['field_name'] = end($_SESSION['type']) . "_number";
//                    $data['port'] = $this->inv_model->get_port_from_connection($_GET["id"]);
//                    if (empty($data['port'])) {
//                        $data['extension'] = null;
//                        $this->load->view('connection_view', $data);
//                    } else {
//                        $data['extension'][0][0] = 'Circuit Type';
//                        $data['extension'][0][1] = 'circuit_type';                        
//                        $data['extension'][1][0] = 'Connector Type';
//                        $data['extension'][1][1] = 'connector_type';
//                        $data['extension'][2][0] = 'Media Type';
//                        $data['extension'][2][1] = 'media_type';                      
//                        $this->load->view('connection_view', $data);
//                    }
//                    break;  
                    
                case "circuit": //selected circuit - display A-End & Z-End Shelves and list of connections
                    $this->load->model('inv_model');
                    $temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = $temp['list'];
                    $_SESSION['previouslevelink'] = $temp['links'];
                    $_SESSION['type'] = $temp['type'];                    
                    //$_SESSION['type'][] = "circuit";
                    $_SESSION['idlist'] = $temp['idlist'];
                    $data['field_name'] = "peerless_order_id";
                    $temp = $this->inv_model->get_circuit($_GET["id"]);
                    $data['circuit'] = $temp['circuit']; //full row of data from circuit table
                    $data['connection_id'] = $temp['connection_id']; //list of connection ids
                    $data['circuit_connection_id'] = $temp['circuit_connection_id'];
                    $data['hierarchy'] = $temp['hierarchy']; // array of shelf hierarchies
                    $data['links'] = $temp['links']; //array of shelf hierarchy links
                    $data['peerless_order_id'] = $temp['peerless_order_id'];
                    $data['circuit_id'] = $temp['circuit_id'];
                    $data['extension'] = null; //no extension (hover) data needed for circuit
                    //get each connection hierarchy info 
                    $data['transmit_hierarchy'] = null;
                    $data['transmit_links'] = null;
                    $data['receive_hierarchy'] = null;
                    $data['receive_links'] = null;                    
                    for($i=0; $i<sizeof($data['connection_id']);$i++){
                        $temp = $this->inv_model->get_full_connection($data['connection_id'][$i]);
                        $data['sub_port'][] = $temp['sub_port']; //array of groups of subports for each connection associated with circuit
                        for($j=0;$j<sizeof($temp['sub_port']);$j++){
                            if(isset($temp['sub_port'][$j]->sub_port_type) && $temp['sub_port'][$j]->sub_port_type == 'Transmit'){
                                $temp_hierarchy = $this->inv_model->get_hierarchy('sub_port', $temp['sub_port'][$j]->sub_port_id);
                                $data['transmit_hierarchy'][] = $temp_hierarchy['list'];
                                $data['transmit_links'][] = $temp_hierarchy['links'];;
                            }else if(isset($temp['sub_port'][$j]->sub_port_type) && $temp['sub_port'][$j]->sub_port_type == 'Receive'){
                                $temp_hierarchy = $this->inv_model->get_hierarchy('sub_port', $temp['sub_port'][$j]->sub_port_id);
                                $data['receive_hierarchy'][] = $temp_hierarchy['list'];
                                $data['receive_links'][] = $temp_hierarchy['links'];;    
                            }   
                        }
                    }
                    //display using special circuit view instead of normal inv_view
                    $this->load->view('circuit_view', $data);
                    break;      
                             
            }
        } else if($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET["type"])){
            switch ($_GET["type"]) {
            case "circuits": //selected circuits - display list of peerless order ids
                    $this->load->model('inv_model');
                    //$temp = $this->inv_model->get_hierarchy($_GET["type"], $_GET["id"]);
                    $_SESSION['list'] = array();
                    $_SESSION['list'][0] = 'Circuits';
                    $_SESSION['previouslevelink'] = array();
                    $_SESSION['previouslevelink'][] = base_url() . "inv/menu?type=circuits";
                    //$_SESSION['type'] = 'circuit'; //$temp['type']; 
                    $_SESSION['type'] = array();
                    $_SESSION['type'][0] = "circuit";
                    $_SESSION['idlist'] = null;
                    $data['field_name'] = "peerless_order_id";
                    $data['circuit'] = $this->inv_model->get_all_circuits();
                    $data['extension'] = null; //no extension (hover) data needed for circuit
                    $this->load->view('inv_view', $data);
                    break;   
            }
            
        } else {
            //country not chosen, display country
            $this->load->view('inv_view', $data);
            $_SESSION['type'][$_SESSION['counter']] = "country";
            $this->load->model('inv_model');
            $data['country'] = $this->inv_model->get_country();
            if (empty($data['country'])) {
                $data['extension'] = null;
                $this->load->view('inv_view', $data);
            } else {
                $data['extension'] = null;
                $this->load->view('inv_view', $data);
            }
        }
    }
    
    function test(){
      $this->load->model('inv_model');
      $this->inv_model->get_circuit(2);
    }

}

//End of inv.php
//Location: application\controllers\inv.php
