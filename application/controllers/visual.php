<?php

session_start();
//echo !isset($_SESSION['list']) . ' ';
if(!isset($_SESSION['list'])){
    $_SESSION['list'] = array();
    $_SESSION['type'] = array();
    //echo "array not set";
}

class Visual extends CI_Controller {
    
    //called from javascript using AJAX
    //returns all slot & port data needed to display visual representation
    function get_visual_shelf(){
        $return_data = array();
        $shelf_id =  $_GET['shelf_id']; //shelf_id sent via GET from AJAX   
        $slot_total = $_GET['slot_total'];
        $this->load->model('inv_model'); //load model
        $slot = $this->inv_model->get_slot($shelf_id); //get all slots & slot data
        
        $html = '<div class="visual-container">';

        $slot_count = 1;
        $number_of_ports = 0;
        if(isset($slot)){
            foreach ($slot as $row){ //for each slot
                $display = $row->slot_number;
                $id = $row->slot_id;
                while($slot_count<$display){   //fill in slot numbers in visual that are not added in db yet
                    $html = $html . '<div class="visual-slot">' . 'Slot ' . $slot_count . '</a><hr></div>';
                    $slot_count++;
                } 
                $html = $html . '<div class="visual-slot"><a class="list" id="' . $id . '" href="' . base_url() . 'inv/menu?type=slot&id=' . $id . '&current=' . $display . '">' . 'Slot ' . $display . '</a><hr>';
                if(strpos($display, ',') !== false || strpos($display, '-') !== false) {//if contains comma or dash takes up 2 slots - increment slot count by 1 extra
                    $slot_count++;
                }
                $slot_count++;
                $number_of_ports = $row->number_of_ports;
                $vertical_numbering = false;
                $block_flag = ''; //flag use to add a class to display ports block style (for port numbering going down instead of across)
                if($number_of_ports > 100 || $number_of_ports <= 12){
                    $vertical_numbering = true;
                    $ports_in_column = 12;
                    $block_flag = ' block'; //add block class to ports to display in vertical order
                }
                
                $sub_slot = $this->inv_model->get_sub_slot($id);
                if(isset($sub_slot)){
                    foreach ($sub_slot as $sub_slot_row){ //for each subslot
                        $port = $this->inv_model->get_port($sub_slot_row->sub_slot_id);
                        if(isset($port)){
                            $i = 1; //start at 1 to keep counting easy and equal to port number
                            foreach ($port as $port_row){
                                if($port_row->port_number > $i){ //if counter not to current filled port, loop through and add empty ports
                                    while($i <= $number_of_ports && $i < $port_row->port_number){
                                        if($vertical_numbering && ($i == 1 || $i%12 == 1)){ //add beginning div for new column
                                            $html = $html . '<div class="float">';
                                        }
                                        $html = $html . '<span class="visual-port unused-port' . $block_flag . '">' . $i . '</span>';
                                        if($vertical_numbering && ($i%12 == 0 || $i == $number_of_ports)){ //add ending div for new column
                                            $html = $html . '</div>';
                                        }
                                        $i++;                                        
                                    }
                                }//now add filled port after empty ports were added
                                if(strpos($port_row->port_number, ',') !== false){ //add 2 ports if port number contains comma
                                    $token = strtok($port_row->port_number, ",");
                                    if($vertical_numbering && ($i == 1 || $i%12 == 1)){ //add beginning div for new column
                                        $html = $html . '<div class="float">';
                                    }
                                    $html = $html . '<span class="visual-port used-port' . $block_flag . '"><a id="' . $port_row->port_id . '" class="port-link" href="' . base_url() . 'inv/menu?type=port&id=' . $port_row->port_id . '&current=' . $token . '">' . $token . '</a></span>';
                                    if($vertical_numbering && ($i%12 == 0 || $i == $number_of_ports)){ //add ending div for new column
                                        $html = $html . '</div>';
                                    }  
                                    $i++; //increment after adding one port
                                    $token = strtok(",");
                                    if($vertical_numbering && ($i == 1 || $i%12 == 1)){ //add beginning div for new column
                                        $html = $html . '<div class="float">';
                                    }
                                    $html = $html . '<span class="visual-port used-port' . $block_flag . '"><a id="' . $port_row->port_id . '" class="port-link" href="' . base_url() . 'inv/menu?type=port&id=' . $port_row->port_id . '&current=' . $token . '">' . $token . '</a></span>';
                                    if($vertical_numbering && ($i%12 == 0 || $i == $number_of_ports)){ //add ending div for new column
                                        $html = $html . '</div>';
                                    }  
                                    $i++; //increment again after adding another port
                                }else{
                                    if($vertical_numbering && ($i == 1 || $i%12 == 1)){ //add beginning div for new column
                                        $html = $html . '<div class="float">';
                                    }                                    
                                    $html = $html . '<span class="visual-port used-port' . $block_flag . '"><a id="' . $port_row->port_id . '" class="port-link" href="' . base_url() . 'inv/menu?type=port&id=' . $port_row->port_id . '&current=' . $port_row->port_number . '">' . $port_row->port_number . '</a></span>';
                                    if($vertical_numbering && ($i%12 == 0 || $i == $number_of_ports)){ //add ending div for new column
                                        $html = $html . '</div>';
                                    }   
                                    $i++;                                    
                                }
                            }
                            while($i <= $number_of_ports){ //add empty ports after last filled port, if any remaining
                                if($vertical_numbering && ($i == 1 || $i%12 == 1)){ //add beginning div for new column
                                    $html = $html . '<div class="float">';
                                }                                 
                                $html = $html . '<span class="visual-port unused-port' . $block_flag . '">' . $i . '</span>';
                                if($vertical_numbering && ($i%12 == 0 || $i == $number_of_ports)){ //add ending div for new column
                                    $html = $html . '</div>';
                                }  
                                $i++;                                
                            }
                        }else{ //if no port records, add all ports as empty ports
                            if($number_of_ports > 0){
                                for($i=1; $i<=$number_of_ports; $i++){
                                    if($vertical_numbering && ($i == 1 || $i%12 == 1)){ //add beginning div for new column
                                        $html = $html . '<div class="float">';
                                    }                                        
                                    $html = $html . '<span class="visual-port unused-port' . $block_flag . '">' . $i . '</span>';
                                    if($vertical_numbering && ($i%12 == 0 || $i == $number_of_ports)){ //add ending div for new column
                                        $html = $html . '</div>';
                                    }                                     
                                }
                            }                            
                        }
                    }
                }else{ //no subslot records
                    if ($number_of_ports > 0) { //add all ports as empty if no sub slot
                        for ($i = 1; $i <= $number_of_ports; $i++) {
                            if($vertical_numbering && ($i == 1 || $i%12 == 1)){ //add beginning div for new column
                                $html = $html . '<div class="float">';
                            }                             
                            $html = $html . '<span class="visual-port unused-port' . $block_flag . '">' . $i . '</span>';
                            if($vertical_numbering && ($i%12 == 0 || $i == $number_of_ports)){ //add ending div for new column
                                $html = $html . '</div>';
                            }                               
                        }
                    }
                }
                $html = $html . '</div>'; //end visual-slot div
            }

            while($slot_count<=$slot_total){   //fill in slot numbers in visual that are not added in db yet
                $html = $html . '<div class="visual-slot">' . 'Slot ' . $slot_count . '</a><hr></div>';
                $slot_count++;
            } 

         $html = $html . '</div>'; //end visual-container div started above               
        }else if($slot_total > 0){
            for($i=1; $i<=$slot_total; $i++){
                $html = $html . '<div class="visual-slot">' . 'Slot ' . $i . '</a><hr></div>';
            }
            $html = $html . '</div>'; //close visual container div
        }else{
            $html = 'No Slots / Ports';    
        }
        
        $return_data['slot_count'] = $slot_count - 1; //count is increased after each slot is added, so subtract one for final count
        $return_data['number_of_ports'] = $number_of_ports;
        $return_data['html'] = $html;
        echo json_encode($return_data);
    }
    
    //called from javascript using AJAX
    //returns all shelf data needed to display visual representation of each shelf and entire bay    
    function get_bay_data(){
        $this->load->model('inv_model');
        $bay_id =  $_GET['bay_id'];
        $bay_height = $_GET['bay_height'];
        $data = $this->inv_model->get_shelf($bay_id);
        $html = '';
        $bay_counter = $bay_height;
        if(isset($data)){
            foreach ($data as $row){

                $shelf_height = $row->height;                
                $top_rack = $row->top_rack_unit;                
                if($bay_counter > $top_rack){
                    $html = $html . '<div style="height: ' . (($bay_counter - $top_rack) * 9) . 'px"></div>';
                    $bay_counter = $bay_counter - ($bay_counter - $top_rack);
                }
                $display = $row->shelf_name;
                $id = $row->shelf_id;
                $html = $html . '<div class="visual-shelf-bay" style="height: '. $shelf_height * 9 . 'px"><a class="list shelf-camera" id="' . $id . '" href="' . base_url() . 'inv/menu?type=shelf&id=' . $id . '&current=' . $display . '">' . $display . '</a></div>';
                $bay_counter -= $shelf_height;
            }
        }else{
            $html = 'No Shelves';
        }
        if($bay_counter > 0){
            $html = $html . '<div style="height: ' . ($bay_counter * 9) . 'px"></div>';
        }
        $return_data['html'] = $html;
        echo json_encode($return_data);
    }    

}