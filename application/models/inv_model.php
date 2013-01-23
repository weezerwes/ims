<?php

error_reporting(E_ALL);

class Inv_model extends CI_Model {

    function get_country() {
        $this->db->order_by('country_id', 'asc');
        $q = $this->db->get('country');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

    function get_city($country_id) {
        $this->db->select('city_id, city_name, state_name');
        $this->db->where('country_id', $country_id);
        $this->db->order_by('city_name', 'asc');
        $q = $this->db->get('city');

        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

    function get_building($city_id) {
        $this->db->select('building_id, building_address, building_zip');
        $this->db->where('city_id', $city_id);
        $this->db->order_by('building_address', 'asc');
        $q = $this->db->get('building');

        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }

    function get_location($building_id) {
        $sql = 'select loc.location_id, loc.location_name, loc.suite_number, ext.external_entity_name
            from location as loc left join external_entity as ext
            on loc.external_entity_id = ext.external_entity_id
            where loc.building_id = ' . $building_id . 
            ' order by loc.location_name';     
            
     
        $q = $this->db->query($sql);
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    function get_aisle($location_id) {
        $this->db->select('aisle_id, aisle_number');
        $this->db->where('location_id', $location_id);
        $this->db->order_by('aisle_number', 'asc');
        $q = $this->db->get('aisle');

        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    function get_bay($aisle_id) {
        $sql = 'select bay.bay_id, bay.bay_number, bay.bay_height, type.bay_type
                from bay as bay left join bay_type as type
                on bay.bay_type_id = type.bay_type_id 
                where bay.aisle_id = ' . $aisle_id . 
                ' order by bay.bay_number';
            
        $q = $this->db->query($sql);
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
                //get multiple bay nicknames
                //left join bay_nickname as nick 
                //on bay.bay_id = nick.bay_id
            }
        }
        return $data;
    }    
    
    function get_shelf($bay_id) {
        $sql = 'select shelf.shelf_id, shelf.shelf_name, shelf.top_rack_unit, type.chassis_type, type.number_of_slots, type.height, type.terminating, manu.manufacturer_name, plat.platform, type.part_number, type.description, shelf.serial_number
            from shelf as shelf left join chassis_type as type
            on shelf.chassis_type_id = type.chassis_type_id
            left join manufacturer as manu
            on type.manufacturer_id = manu.manufacturer_id
            left join platform as plat
            on type.platform_id = plat.platform_id            
            where shelf.bay_id = ' . $bay_id . 
            ' order by CAST(shelf.top_rack_unit  AS SIGNED) DESC';
        $q = $this->db->query($sql);
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }
    
    function get_shelf_platform($shelf_id){
        $this->db->select('platform');
        $this->db->join('chassis_type', 'chassis_type.platform_id = platform.platform_id');
        $this->db->join('shelf', 'shelf.chassis_type_id = chassis_type.chassis_type_id');
        $this->db->where('shelf_id', $shelf_id);
        return $this->db->get('platform');
    }    
    
    function get_slot_type_from_port($port_id){
        $this->db->select('slot_type.slot_type');
        $this->db->join('sub_slot', 'port.sub_slot_id = sub_slot.sub_slot_id');
        $this->db->join('slot', 'sub_slot.slot_id = slot.slot_id');
        $this->db->join('slot_type', 'slot.slot_type_id = slot_type.slot_type_id');
        $this->db->where('port_id', $port_id);
        $q = $this->db->get('port');
        $slot_type = null;
        if ($q->num_rows() > 0) {
            $slot_type = $q->$row()->slot_type;
        }
        return $slot_type;
    }
    
    function get_all_terminating_shelves(){
        //get all shelves for list used in dropdown of circuit (A end & Z end shelves)
        $sql = 'SELECT DISTINCT shelf_id, shelf_name
                FROM shelf
                JOIN chassis_type ON shelf.chassis_type_id = chassis_type.chassis_type_id
                WHERE terminating =  \'Terminating\'';

        $q = $this->db->query($sql);
        //define $data array and set to null
        $data = null;
        $data['shelves'] = array();
        $data['hierarchy'] = array();
        if ($q->num_rows() > 0) {
            //for each port, add to unused port array and get hierarchy
            foreach ($q->result() as $row) {
                $data['shelves'][] = $row->shelf_id;
                $hierarchy = '';
                $temp = $this->inv_model->get_dropdown_hierarchy('shelf', $row->shelf_id);   
                for($j=3;$j<sizeof($temp['list']); $j++){
                    if($j == 3){
                        $hierarchy = $hierarchy . $temp['list'][$j];
                    }else{
                        $hierarchy = $hierarchy . ' ► ' . $temp['list'][$j];
                    }
                }
                $data['hierarchy'][] = $hierarchy;               
            }
        }
        
        //sort hierarchies for dropdown - keep arrays in order by sorting other 2 with it
        array_multisort($data['hierarchy'], $data['shelves'], SORT_NUMERIC);            
        return $data;        
    }     
    
    function get_all_shelves(){
        //get all shelves for list used in dropdown of circuit (A end & Z end shelves)
        $sql = 'SELECT DISTINCT shelf_id, shelf_name
                FROM shelf';

        $q = $this->db->query($sql);
        //define $data array and set to null
        $data = null;
        $data['shelves'] = array();
        $data['hierarchy'] = array();
        if ($q->num_rows() > 0) {
            //for each port, add to unused port array and get hierarchy
            foreach ($q->result() as $row) {
                $data['shelves'][] = $row->shelf_id;
                $hierarchy = '';
                $temp = $this->inv_model->get_hierarchy('shelf', $row->shelf_id);   
                for($j=3;$j<sizeof($temp['list']); $j++){
                    if($j == 3){
                        $hierarchy = $hierarchy . $temp['list'][$j];
                    }else{
                        $hierarchy = $hierarchy . ' ► ' . $temp['list'][$j];
                    }
                }
                $data['hierarchy'][] = $hierarchy;               
            }
        }
        
        //sort hierarchies for dropdown - keep arrays in order by sorting other 2 with it
        array_multisort($data['hierarchy'], $data['shelves'], SORT_NUMERIC);            
        return $data;        
    }  
    
    function get_one_shelf_from_circuit($circuit_id, $termination_type){
        $sql = 'select cs.shelf_id
            from circuit_shelf as cs
            where cs.circuit_id = ' . $circuit_id .
                ' and cs.termination_type = \'' . $termination_type . '\'';

        $q = $this->db->query($sql);
        $shelf_id = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $shelf_id = $row->shelf_id;
            }
        }
        return $shelf_id;        
    }    
    
    function get_number_of_open_slots_of_shelf($shelf_id){
        $this->db->select('chassis_type.number_of_slots - COUNT( slot_id ) AS open_slots');
        $this->db->join('shelf', 'slot.shelf_id = shelf.shelf_id');
        $this->db->join('chassis_type', 'shelf.chassis_type_id = chassis_type.chassis_type_id');
        $this->db->where('slot.shelf_id', $shelf_id);
        $q = $this->db->get('slot');
        return $q->row()->open_slots;        
    }
    
    function get_slot($shelf_id) {
        $sql = 'select slot.slot_id, slot.slot_number, slot.serial_number, slot.notes, type.slot_type, type.slot_orientation, type.number_of_ports, type.number_of_sub_slots, type.number_of_ports_wide, type.number_of_ports_tall, type.port_numbering, manu.manufacturer_name, plat.platform, type.description
            from slot as slot left join slot_type as type
            on slot.slot_type_id = type.slot_type_id
            left join manufacturer as manu 
            on type.manufacturer_id = manu.manufacturer_id      
            left join platform as plat
            on type.platform_id = plat.platform_id               
            where slot.shelf_id = ' . $shelf_id . 
            ' order by CAST(slot.slot_number  AS SIGNED) ASC';        
        $q = $this->db->query($sql);
        
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                if($row->number_of_sub_slots == 0){
                    $row->number_of_sub_slots = null;
                }
                $data[] = $row;
            }
        }
        return $data;
    }  
    
    function get_slot_platform_from_slot_type($slot_type_id){
        $this->db->select('platform');
        $this->db->join('slot_type', 'slot_type.platform_id = platform.platform_id');
        $this->db->where('slot_type_id', $slot_type_id);
        return $this->db->get('platform');    
    }
   
    function get_slot_platform_from_slot_id($slot_id){
        $this->db->select('platform');
        $this->db->join('slot_type', 'slot_type.platform_id = platform.platform_id');
        $this->db->join('slot', 'slot.slot_type_id = slot_type.slot_type_id');
        $this->db->where('slot_id', $slot_id);
        return $this->db->get('platform');  
    }    
    
    function get_sub_slot($slot_id) {
        $sql = 'select sub_slot.sub_slot_id, sub_slot.sub_slot_number, sub_slot.front_slot, sub_slot.serial_number, sub_slot.notes, type.sub_slot_type, type.sub_slot_orientation, type.number_of_ports, type.number_of_ports_wide, type.number_of_ports_tall, type.port_numbering, manu.manufacturer_name, plat.platform, type.description
            from sub_slot as sub_slot left join sub_slot_type as type
            on sub_slot.sub_slot_type_id = type.sub_slot_type_id
            left join manufacturer as manu 
            on type.manufacturer_id = manu.manufacturer_id 
            left join platform as plat
            on type.platform_id = plat.platform_id               
            where sub_slot.slot_id = ' . $slot_id . 
            ' order by CAST(sub_slot.sub_slot_number  AS SIGNED) ASC';
        $q = $this->db->query($sql);
        
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }  
    
    function get_sub_slot_platform_from_sub_slot_type($sub_slot_type_id){
        $this->db->select('platform');
        $this->db->join('sub_slot_type', 'sub_slot_type.platform_id = platform.platform_id');
        $this->db->where('sub_slot_type_id', $sub_slot_type_id);
        return $this->db->get('platform');
    }        
    
    function get_number_of_open_sub_slots_of_slot($slot_id){
        $this->db->select('slot_type.number_of_sub_slots - COUNT( sub_slot_id ) AS open_sub_slots');
        $this->db->join('slot', 'sub_slot.slot_id = slot.slot_id');
        $this->db->join('slot_type', 'slot.slot_type_id = slot_type.slot_type_id');
        $this->db->where('sub_slot.slot_id', $slot_id);
        $q = $this->db->get('sub_slot');
        return $q->row()->open_sub_slots;        
    }    
    
    function get_port($sub_slot_id) {
        $sql = 'select port.port_id, port.port_number, type.connector_type, media.media_type, circuit.circuit_type
            from port as port left join connector_type as type 
            on port.connector_type_id = type.connector_type_id
            left join media_type as media
            on port.media_type_id = media.media_type_id
            left join circuit_type as circuit
            on port.circuit_type_id = circuit.circuit_type_id
            where port.sub_slot_id = ' . $sub_slot_id .
            ' order by CAST(port.port_number  AS SIGNED) ASC';

        $q = $this->db->query($sql);
        
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }  
    
    function get_number_of_open_ports_of_sub_slot($sub_slot_id){
        $this->db->select('sub_slot_number');
        $this->db->where('sub_slot_id', $sub_slot_id);
        $q = $this->db->get('sub_slot');
        $sub_slot_number = $q->row()->sub_slot_number;   
        if($sub_slot_number === '0'){
            $this->db->select('slot_type.number_of_ports - COUNT( port_id ) AS open_ports');
            $this->db->join('sub_slot', 'port.sub_slot_id = sub_slot.sub_slot_id');
            $this->db->join('slot', 'sub_slot.slot_id = slot.slot_id');
            $this->db->join('slot_type', 'slot.slot_type_id = slot_type.slot_type_id');
            $this->db->where('port.sub_slot_id', $sub_slot_id);
            $q = $this->db->get('port');            
            return $q->row()->open_ports;
        }else{
            $this->db->select('sub_slot_type.number_of_ports - COUNT( port_id ) AS open_ports');
            $this->db->join('sub_slot', 'port.sub_slot_id = sub_slot.sub_slot_id');
            $this->db->join('sub_slot_type', 'sub_slot.sub_slot_type_id = sub_slot_type.sub_slot_type_id');
            $this->db->where('port.sub_slot_id', $sub_slot_id);
            $q = $this->db->get('port');
            return $q->row()->open_ports;          
        }
    }
    
    function get_sub_port($port_id) {
        $sql = 'select sub_port.sub_port_id, sub_port.sub_port_type, sub_port.connection_id
            from sub_port as sub_port 
            where sub_port.port_id = ' . $port_id .
            ' order by sub_port.sub_port_type DESC';

        $q = $this->db->query($sql);
        
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;
    }     
    
    function get_one_port_from_connection($connection_id, $sub_port_type) {
        $sql = 'select sub_port.port_id
            from sub_port as sub_port 
            where sub_port.connection_id = ' . $connection_id .
                ' and sub_port.sub_port_type = \'' . $sub_port_type . '\'';

        $q = $this->db->query($sql);
        $port_id = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $port_id = $row->port_id;
            }
        }
        return $port_id;
    }   
    
    function get_connection($sub_port_id) {
        $sql = 'select sub_port.port_id, sub_port.sub_port_id, sub_port.connection_id
            from sub_port as sub_port left join connection as connection
            on sub_port.connection_id = connection.connection_id
            where sub_port.sub_port_id = ' . $sub_port_id .
                ' order by connection.connection_id';

        $q = $this->db->query($sql);
        $connection = null;
        //define $data array and set to null
        $data = null;
        if ($q) {
            if ($q->num_rows() > 0) {
                foreach ($q->result() as $row) {
                    $data['connection'][] = $row;
                    $connection = $row->connection_id;
                    if(isset($connection)){
                        $sql = 'select ext.external_order_id, ext.external_connection_id, ext.external_ticket_id, ent.external_entity_name
                        from connection_external_relation as ext left join external_entity as ent
                        on ext.external_entity_id = ent.external_entity_id
                        where ext.connection_id = ' . $connection .
                            ' order by ent.external_entity_name';
                        $subq = $this->db->query($sql);
                        if ($subq->num_rows() > 0) {
                            foreach ($subq->result() as $subrow) {
                                $data['ext'][] = $subrow;
                            }
                        }                        
                    }else{ //if no connection, return no data at all
                        $data = null;
                    }
                }
            }
        } else {
            $_SESSION['error_message'] = 'Connection has not been assigned any sub-ports!';
        }
        return $data;
    }
    
    function get_full_connection($connection_id) {
        $sql = 'select sub_port.sub_port_id, sub_port.port_id, sub_port.connection_id, sub_port.sub_port_type
            from sub_port as sub_port left join connection as connection
            on sub_port.connection_id = connection.connection_id
            where sub_port.connection_id = ' . $connection_id .
                ' order by sub_port.sub_port_type DESC';

        $q = $this->db->query($sql);
        $connection = null;
        //define $data array and set to null
        $data = null;
        $data['sub_port'] = null;
        $external_data_loaded = false;
        if ($q) {
            if ($q->num_rows() > 0) {
                foreach ($q->result() as $row) {
                    $data['sub_port'][] = $row;
                    $connection = $row->connection_id;
                    $sql = 'select ext.external_order_id, ext.external_connection_id, ext.external_ticket_id, ent.external_entity_name
                    from connection_external_relation as ext left join external_entity as ent
                    on ext.external_entity_id = ent.external_entity_id
                    where ext.connection_id = ' . $connection .
                            ' order by ent.external_entity_name';
                    $subq = $this->db->query($sql);
                    if ($subq->num_rows() > 0 && !$external_data_loaded) {
                        foreach ($subq->result() as $subrow) {
                            $data['ext'][] = $subrow;
                            $external_data_loaded = true;
                        }
                    }
                }
            }
        } else {
            $_SESSION['error_message'] = 'Connection has not been assigned any sub-ports!';
        }

        $sql = 'select cc.circuit_id, circuit.peerless_order_id, cc.circuit_connection_id
                from circuit_connection as cc
                left join circuit as circuit 
                on cc.circuit_id = circuit.circuit_id
                where cc.connection_id = ' . $connection_id .
                    ' order by circuit.peerless_order_id';
        $subq2 = $this->db->query($sql);
        $data['circuit_id'] = array();
        $data['peerless_order_id'] = array();
        $data['circuit_connection_id'] = array();
        if($subq2){
            if ($subq2->num_rows() > 0) {
                foreach ($subq2->result() as $subrow) {
                    $data['circuit_id'][] = $subrow->circuit_id;
                    $data['peerless_order_id'][] = $subrow->peerless_order_id;
                    $data['circuit_connection_id'][] = $subrow->circuit_connection_id;
                }
            }
        }
        return $data;
    }
    
    function get_connections_with_assigned_circuit($connections, $circuit_id){
        $connections_assigned_to_circuits = null;
        for($i=0;$i<sizeof($connections);$i++){
            $this->db->select('connection_id, circuit_id');
            $this->db->where('connection_id', $connections[$i]);
            $q = $this->db->get('circuit_connection');            
            if($q->num_rows > 0){
                $temp = $q->row()->circuit_id;
                if($q->row()->circuit_id != $circuit_id){
                    $connections_assigned_to_circuits[] = $q->row()->connection_id;  
                }
            }
        }
        return $connections_assigned_to_circuits;
    }

    function get_available_ports($sub_port_type, $sub_port_id=null){ //returns sorted arrays of subslot ids, associated hierarchies, and array of open ports for each subslot
        //get any port that does not have a connection associated to one of it's subports
        $sql = 'SELECT DISTINCT port_id
                FROM sub_port AS sub_port
                WHERE sub_port.connection_id IS NULL 
                and sub_port.sub_port_type = \'' . $sub_port_type . '\'';
        if(isset($sub_port_id)){ //if creating connection after navigating to subport level in hierarchy, return only 1 port based on subport id
            $sql = $sql . ' and sub_port.sub_port_id = ' . $sub_port_id;
        }

        $q = $this->db->query($sql);
        //define $data array and set to null
        $data = null;
        $data['unused_ports'] = array();
        $data['hierarchy'] = array();
        if ($q->num_rows() > 0) {
            //for each port, add to unused port array and get hierarchy
            foreach ($q->result() as $row) {
                $data['unused_ports'][] = $row->port_id;
                $hierarchy = '';
                $temp = $this->inv_model->get_dropdown_hierarchy('port', $row->port_id);   
                for($j=3;$j<sizeof($temp['list']); $j++){
                    if($j == 3){
                        $hierarchy = $hierarchy . $temp['list'][$j];
                    }else{
                        if($temp['list'][$j] != 'Sub Slot 0'){
                            $hierarchy = $hierarchy . ' ► ' . $temp['list'][$j];
                        }                        
                    }
                }
                $data['hierarchy'][] = $hierarchy;               
            }
        }
        
        //sort hierarchies for dropdown - keep arrays in order by sorting other 2 with it
        array_multisort($data['hierarchy'], $data['unused_ports'], SORT_NUMERIC);            
        return $data;        
    }

//    function get_port_from_connection($connection_id) {
//        $sql = 'select concat(port.port_number, \' \', sub_port.sub_port_type) as port_number, connection.connection_id, circuit.peerless_order_id
//            from connection as connection left join sub_port as sub_port
//            on connection.connection_id = sub_port.connection_id
//            left join port as port
//            on port.port_id = sub_port.port_id
//            left join circuit as circuit
//            on circuit.circuit_id = connection.circuit_id
//            where connection.connection_id = ' . $connection_id . 
//            ' order by CAST(port.port_number  AS SIGNED) ASC';        
//                                     
//        $q = $this->db->query($sql);
//        
//        //define $data array and set to null
//        $data = null;
//        if ($q->num_rows() > 0) {
//            foreach ($q->result() as $row) {
//                $data[] = $row;
//            }
//        }
//        return $data;
//    }    
//    
    function get_all_circuits(){
        $sql = 'select distinct circuit.circuit_id, circuit.peerless_order_id
            from circuit as circuit 
            left join circuit_shelf as cs
            on circuit.circuit_id = cs.circuit_id
            left join shelf as shelf
            on cs.shelf_id = shelf.shelf_id' . 
            ' order by circuit.peerless_order_id';        
        $q = $this->db->query($sql);
        
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data[] = $row;
            }
        }
        return $data;   
    }
    
    function get_circuit($circuit_id) {
        $sql = 'select circuit.circuit_id, circuit.peerless_order_id, cs.circuit_shelf_id, cs.shelf_id, shelf.shelf_name, cs.termination_type
            from circuit as circuit 
            left join circuit_shelf as cs
            on circuit.circuit_id = cs.circuit_id
            left join shelf as shelf
            on cs.shelf_id = shelf.shelf_id
            where circuit.circuit_id = ' . $circuit_id . 
            ' order by circuit.peerless_order_id';
      
        $q = $this->db->query($sql);
        
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['circuit'][] = $row;
                $data['peerless_order_id'] = $row->peerless_order_id;
                $data['circuit_id'] = $row->circuit_id;
                $temp = $this->get_hierarchy('shelf', $row->shelf_id); 
                $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                $data['links'][] = $temp['links']; //get links for hierarchy from temp return data                
            }
        }
        //get all connections associated with this circuit id
        $sql = 'select cc.connection_id, cc.circuit_connection_id
            from circuit_connection as cc
            where cc.circuit_id = ' . $circuit_id . 
            ' order by cc.connection_id';

        $q = $this->db->query($sql);
        
        //define $data array and set to null
        $data['connection_id'] = array();
        $data['circuit_connection_id'] = array();
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['connection_id'][] = $row->connection_id;
                $data['circuit_connection_id'][] = $row->circuit_connection_id;
            }
        }
        return $data;
    }      
    
    function get_tables(){
        return $this->db->list_tables();
        
    }
    
    function get_dropdown_hierarchy($type, $id){
        if($type == 'port'){
        $sql='select location.location_name, aisle.aisle_number, bay.bay_number, shelf.shelf_name, slot.slot_number, sub_slot.sub_slot_number, port.port_number
            from port as port
            left join sub_slot as sub_slot 
            on port.sub_slot_id = sub_slot.sub_slot_id
            left join slot as slot
            on sub_slot.slot_id = slot.slot_id
            left join shelf as shelf
            on slot.shelf_id = shelf.shelf_id
            left join bay as bay
            on shelf.bay_id = bay.bay_id
            left join aisle as aisle
            on bay.aisle_id = aisle.aisle_id
            left join location as location
            on aisle.location_id = location.location_id
            where port.port_id = ' . $id;
        }else if($type == 'shelf'){
        $sql='select location.location_name, aisle.aisle_number, bay.bay_number, shelf.shelf_name
            from shelf as shelf
            left join bay as bay
            on shelf.bay_id = bay.bay_id
            left join aisle as aisle
            on bay.aisle_id = aisle.aisle_id
            left join location as location
            on aisle.location_id = location.location_id
            where shelf.shelf_id = ' . $id;            
        }
        
        $q = $this->db->query($sql);
        $data = null;
        if ($q->num_rows() > 0) {
            $row = $q->row();
            $data['list'][0] = ''; //leave first three blank because hierarchy starts with location level in dropdown
            $data['list'][1] = '';
            $data['list'][2] = '';
            $data['list'][3] = $row->location_name;
            $data['list'][4] = 'Aisle ' . ($row->aisle_number < 10 && $row->aisle_number > 0 ? "0" : "") . $row->aisle_number;
            $data['list'][5] = 'Bay ' . ($row->bay_number < 10 && $row->bay_number > 0 ? "0" : "") . $row->bay_number;
            $data['list'][6] = $row->shelf_name;
            if($type == 'port'){
                $data['list'][7] = 'Slot ' . ($row->slot_number < 10 && $row->slot_number > 0 ? "0" : "") . $row->slot_number;
                $data['list'][8] = 'Sub Slot ' . ($row->sub_slot_number < 10 && $row->sub_slot_number > 0 ? "0" : "") . $row->sub_slot_number;
                $data['list'][9] = 'Port ' . ($row->port_number < 10 && $row->port_number > 0 ? "0" : "") . $row->port_number;     
            }
        }        
        return $data;
    }
    /***************
     * get_hierarchy takes a type & an id and returns the hierarchy based on that level.
     * Except for the first level (country), every other case pulls the current level,
     * recursively calls the get_hierarchy method passing one level up, then adds the 
     * current level to the returned hierarchy 
     **************/
    function get_hierarchy($type, $id, $port_id = 0) {
        $hierarchy['list'] = array(); //list of hierarchy values
        $hierarchy['links'] = array(); //links to each level of hierarchy
        $hierarchy['type'] = array(); //list of type for each level of hierarchy
        $hierarchy['idlist'] = array(); //list of ids for each level of hierarchy
        switch ($type) {
            case "country":
                $data = $this->get_one_country($id);
                $hierarchy['list'][] = $data;
                $hierarchy['links'][] = base_url() . "inv/menu?type=country&id=" . $id . "&current=" . $data;
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "city": //add country to hierarchy array
                $data = $this->get_city_parent($id);
                $hierarchy['list'][] = $data['value'];
                $hierarchy['links'][] = base_url() . "inv/menu?type=city&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                $new_id = $data['new_id'];
                $data = $this->get_one_country($new_id);
                array_unshift($hierarchy['links'], base_url() . "inv/menu?type=country&id=" . $new_id . "&current=" . $data);
                array_unshift($hierarchy['list'], $data);
                array_unshift($hierarchy['type'], 'country');
                array_unshift($hierarchy['idlist'], $new_id);
                break;
            case "building":
                $data = $this->get_building_parent($id);
                $hierarchy = $this->get_hierarchy('city', $data['new_id']);                
                $hierarchy['list'][] = $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=building&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "location":
                $data = $this->get_location_parent($id);            
                $hierarchy = $this->get_hierarchy('building', $data['new_id']);
                $hierarchy['list'][] = $data['value'];
                $hierarchy['links'][] = base_url() . "inv/menu?type=location&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "aisle":
                $data = $this->get_aisle_parent($id);            
                $hierarchy = $this->get_hierarchy('location', $data['new_id']);
                $hierarchy['list'][] = $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=aisle&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "bay":
                $data = $this->get_bay_parent($id);            
                $hierarchy = $this->get_hierarchy('aisle', $data['new_id']);
                $hierarchy['list'][] = $data['value'];
                $hierarchy['links'][] = base_url() . "inv/menu?type=bay&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "shelf":
                $data = $this->get_shelf_parent($id);            
                $hierarchy = $this->get_hierarchy('bay', $data['new_id']);
                $hierarchy['list'][] = $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=shelf&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "slot":
                $data = $this->get_slot_parent($id);            
                $hierarchy = $this->get_hierarchy('shelf', $data['new_id']);
                $hierarchy['list'][] = $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=slot&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "sub_slot":
                $data = $this->get_sub_slot_parent($id);            
                $hierarchy = $this->get_hierarchy('slot', $data['new_id']);
                $hierarchy['list'][] = $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=sub_slot&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "port":
                $data = $this->get_port_parent($id);            
                $hierarchy = $this->get_hierarchy('sub_slot', $data['new_id']);
                $hierarchy['list'][] = $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=port&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
            case "sub_port":
                $data = $this->get_sub_port_parent($id);            
                $hierarchy = $this->get_hierarchy('port', $data['new_id']);
                $hierarchy['list'][] = $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=sub_port&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;            
            case "connection":
                $data = $this->get_connection_parent($id, $port_id);            
                $hierarchy = $this->get_hierarchy('sub_port', $data['new_id']);
                $hierarchy['list'][] = $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=connection&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = $type;
                $hierarchy['idlist'][] = $id;
                break;
           case "circuit":
                $data = $this->get_one_circuit($id);  
                $hierarchy['list'][] = 'Circuits';
                $hierarchy['list'][] = 'Circuit ' . $data['value']; 
                $hierarchy['links'][] = base_url() . "inv/menu?type=circuits";
                $hierarchy['links'][] = base_url() . "inv/menu?type=circuit&id=" . $id . "&current=" . $data['value'];
                $hierarchy['type'][] = 'circuits';
                $hierarchy['type'][] = 'circuit';
                $hierarchy['idlist'][] = '';
                $hierarchy['idlist'][] = $id;
                break;
        }
        return $hierarchy;
    }
    //returns country name given country_id
    function get_one_country($country_id){
        $this->db->where('country_id', $country_id);
        $q = $this->db->get('country');
        //define $data array and set to null
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                return $row->country_name;
            }
        }
    }
    //returns id of parent and current city_name
    function get_city_parent($city_id){
        $this->db->select('country_id, city_name, state_name');
        $this->db->where('city_id', $city_id);
        $q = $this->db->get('city');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->country_id;
                $data['value'] = $row->city_name . ', ' . $row->state_name;
            }
        }
        return $data;
    }  
    //returns id of parent and current building_address
    function get_building_parent($building_id){
        $this->db->select('city_id, building_address');
        $this->db->where('building_id', $building_id);
        $q = $this->db->get('building');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->city_id;
                $data['value'] = $row->building_address;
            }
        }
        return $data;
    }
    //returns id of parent and current location_name
    function get_location_parent($location_id){
        $this->db->select('building_id, location_name');
        $this->db->where('location_id', $location_id);
        $q = $this->db->get('location');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->building_id;
                $data['value'] = $row->location_name;
            }
        }
        return $data;        
    }
    //returns id of parent and current aisle_name
    function get_aisle_parent($aisle_id){
        $this->db->select('location_id, aisle_number');
        $this->db->where('aisle_id', $aisle_id);
        $q = $this->db->get('aisle');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->location_id;
                $data['value'] = 'Aisle ' . ($row->aisle_number < 10 && $row->aisle_number > 0?'0'.$row->aisle_number:$row->aisle_number);
            }
        }
        return $data;         
    }
    //returns id of parent and current bay_number
    function get_bay_parent($bay_id){
        $this->db->select('aisle_id, bay_number');
        $this->db->where('bay_id', $bay_id);
        $q = $this->db->get('bay');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->aisle_id;
                $data['value'] = 'Bay ' . ($row->bay_number < 10 && $row->bay_number > 0?'0'.$row->bay_number:$row->bay_number);
            }
        }
        return $data;         
    }
    //returns id of parent and current shelf_name
    function get_shelf_parent($shelf_id){
        $this->db->select('bay_id, shelf_name');
        $this->db->where('shelf_id', $shelf_id);
        $q = $this->db->get('shelf');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->bay_id;
                $data['value'] = ($row->shelf_name < 10 && $row->shelf_name > 0?'0'.$row->shelf_name:$row->shelf_name);
            }
        }
        return $data;         
    }  
    //returns id of parent and current slot_number
    function get_slot_parent($slot_id){
        $this->db->select('shelf_id, slot_number');
        $this->db->where('slot_id', $slot_id);
        $q = $this->db->get('slot');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->shelf_id;
                $data['value'] = 'Slot ' . ($row->slot_number < 10 && $row->slot_number > 0?'0'.$row->slot_number:$row->slot_number);
            }
        }
        return $data;         
    }   
    //returns id of parent and current sub_slot_number
    function get_sub_slot_parent($sub_slot_id){
        $this->db->select('slot_id, sub_slot_number');
        $this->db->where('sub_slot_id', $sub_slot_id);
        $q = $this->db->get('sub_slot');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->slot_id;
                $data['value'] = 'Sub Slot ' . ($row->sub_slot_number < 10 && $row->sub_slot_number > 0?'0'.$row->sub_slot_number:$row->sub_slot_number);
            }
        }
        return $data;         
    }
    //returns id of parent and current port_number
    function get_port_parent($port_id){
        $this->db->select('sub_slot_id, port_number');
        $this->db->where('port_id', $port_id);
        $q = $this->db->get('port');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->sub_slot_id;
                $data['value'] = 'Port ' . ($row->port_number < 10 && $row->port_number > 0?'0'.$row->port_number:$row->port_number);
            }
        }
        return $data;         
    }    
    //returns id of parent and current sub_port_type
    function get_sub_port_parent($sub_port_id){
        $this->db->select('port_id, sub_port_type');
        $this->db->where('sub_port_id', $sub_port_id);
        $q = $this->db->get('sub_port');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->port_id;
                $data['value'] = $row->sub_port_type;
            }
        }
        return $data;         
    }       
    
    function get_connection_parent($connection_id, $port_id){
        if($port_id != 0){ //if port_id passed as argument (not 0) use it to pull appropriate hierarchy 
            $sql = 'select sub_port.connection_id, sub_port.port_id, sub_port.sub_port_id
            from connection as connection left join sub_port as sub_port
            on sub_port.connection_id = connection.connection_id
            where sub_port.connection_id = ' . $connection_id . 
            ' and sub_port.port_id = ' . $port_id;            
        }else{ //if no port_id passed, pull connection data anyway and use any port data returned
            $sql = 'select sub_port.connection_id, sub_port.port_id, sub_port.sub_port_id
            from connection as connection left join sub_port as sub_port
            on sub_port.connection_id = connection.connection_id
            where connection.connection_id = ' . $connection_id;
        }
                    
        $q = $this->db->query($sql);
        //define $data array and set to null
        $data = null;
        if($q){
            if ($q->num_rows() > 0) {
                foreach ($q->result() as $row) {
                    $data['new_id'] = $row->sub_port_id;
                    $data['value'] = 'Connection ' . $row->connection_id;
                }
            }
        }
        return $data;         
    }    
    
    function get_one_circuit($circuit_id){
        $this->db->select('circuit_id, peerless_order_id');
        $this->db->where('circuit_id', $circuit_id);
        $q = $this->db->get('circuit');
        //define $data array and set to null
        $data = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $data['new_id'] = $row->circuit_id;
                $data['value'] = $row->peerless_order_id;
            }
        }
        return $data;         
    }        

    //Search function for all tables - searches all attributes and related data for type
    function search($type, $search, $hierarchy_flag) {
        switch ($type) {
            case "city":
                if ($hierarchy_flag != 'true') {
                    $this->db->select('city_id, city_name, state_name');
                    $this->db->like('city_name', $search);
                    $this->db->order_by('city_name', 'asc');                    
                } else {
                    $this->db->select('city_id, city_name, state_name');
                    $this->db->order_by('city_name', 'asc');
                }
                $q = $this->db->get('city');
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->city_id);
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data
                        }
                    }
                }
                $_SESSION['field'] = $type . '_name';
                return $data;
                break;

            case "building":
                if ($hierarchy_flag != 'true') {
                    $this->db->select('building_id, building_address, building_zip, city_id');
                    $this->db->like('building_address', $search);
                    $this->db->or_like('building_zip', $search);
                    $this->db->order_by('building_zip', 'asc');
                } else {
                    $this->db->select('building_id, building_address, building_zip, city_id');
                    $this->db->order_by('building_zip', 'asc');
                }
                $q = $this->db->get('building');                
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->building_id); 
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data
                        }
                    }
                }
                if (empty($data['results'])) {
                    $data['extension'] = null;
                } else {
                    $data['extension'][0][0] = 'Zip Code';
                    $data['extension'][0][1] = 'building_zip';
                }                
                $_SESSION['field'] = $type . '_address';
                return $data;                
                break;
                
            case "location":
               if($hierarchy_flag != 'true'){
                    $sql = 'select loc.location_id, loc.location_name, loc.suite_number, ext.external_entity_name
                        from location as loc left join external_entity as ext
                        on loc.external_entity_id = ext.external_entity_id
                        where loc.location_name like \'%' . $search . '%\''.
                        ' or loc.suite_number like \'%' . $search . '%\''.
                        ' or ext.external_entity_name like \'%' . $search . '%\''.
                        ' order by loc.location_name';
               }else{
                    $sql = 'select loc.location_id, loc.location_name, loc.suite_number, ext.external_entity_name
                        from location as loc left join external_entity as ext
                        on loc.external_entity_id = ext.external_entity_id' . 
                        ' order by loc.location_name';
               }
                
                $q = $this->db->query($sql);
                //define $data array and set to null
                $data = null;
                 if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->location_id);
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list'];
                            $data['links'][] = $temp['links'];
                        }
                    }
                }

                if (empty($data['results'])) {
                    $data['extension'] = null;
                } else {
                        $data['extension'][0][0] = 'Suite';
                        $data['extension'][0][1] = 'suite_number';
                        $data['extension'][1][0] = 'Controlling Party';
                        $data['extension'][1][1] = 'external_entity_name';   
                }                
                $_SESSION['field'] = $type . '_name';
                return $data;                
                break;
            case "aisle":
                if($hierarchy_flag != 'true'){
                    $this->db->select('aisle_id, aisle_number');
                    $this->db->like('aisle_number', $search);
                    $this->db->order_by('aisle_number', 'asc');
                }else{
                    $this->db->select('aisle_id, aisle_number');
                    $this->db->order_by('aisle_number', 'asc');                    
                }
                $q = $this->db->get('aisle');
                //define $data array and set to null
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->aisle_id); 
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {                        
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data                        
                        }
                    }
                }
                if (empty($data['results'])) {
                    $data['extension'] = null;
                } else {
                    $data['extension'] = null;
                }
                $_SESSION['field'] = $type . '_number';
                return $data;
                break;
            case "bay":
                if($hierarchy_flag != 'true'){
                    $sql = 'select bay.bay_id, bay.bay_number, bay.bay_height, type.bay_type, nick.bay_nickname
                    from bay as bay left join bay_type as type
                    on bay.bay_type_id = type.bay_type_id 
                    left join bay_nickname as nick 
                    on bay.bay_id = nick.bay_id 
                    where bay.bay_number like \'%' . $search . '%\'' .
                        ' or bay.bay_height like \'%' . $search . '%\'' .
                        ' or type.bay_type like \'%' . $search . '%\'' .
                        ' or nick.bay_nickname like \'%' . $search . '%\'' .
                        ' order by bay.bay_number';
                }else{
                    $sql = 'select bay.bay_id, bay.bay_number, bay.bay_height, type.bay_type, nick.bay_nickname
                    from bay as bay left join bay_type as type
                    on bay.bay_type_id = type.bay_type_id 
                    left join bay_nickname as nick 
                    on bay.bay_id = nick.bay_id
                        order by bay.bay_number';                    
                }
                $q = $this->db->query($sql);
                //define $data array and set to null
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->bay_id); 
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {                                                
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data                        
                        }
                    }
                }
                if (empty($data['results'])) {
                    $data['extension'] = null;
                } else {
                    $data['extension'][0][0] = 'Bay Type';
                    $data['extension'][0][1] = 'bay_type';
                    $data['extension'][1][0] = 'Height';
                    $data['extension'][1][1] = 'bay_height';
                    $data['extension'][2][0] = 'Bay Nickname';
                    $data['extension'][2][1] = 'bay_nickname';
                }
                $_SESSION['field'] = $type . '_number';
                return $data;
                break;
            case "shelf":
                if ($hierarchy_flag != 'true') {
                    $sql = 'select shelf.shelf_id, shelf.shelf_name, shelf.top_rack_unit, type.chassis_type, type.number_of_slots, type.height, type.terminating, manu.manufacturer_name, plat.platform, type.part_number, type.description, shelf.serial_number
                        from shelf as shelf left join chassis_type as type
                        on shelf.chassis_type_id = type.chassis_type_id
                        join manufacturer as manu 
                        on type.manufacturer_id = manu.manufacturer_id
                        left join platform as plat
                        on type.platform_id = plat.platform_id                          
                        where shelf.shelf_name like \'%' . $search . '%\'' .
                            ' or type.chassis_type like \'%' . $search . '%\'' .
                            ' or shelf.top_rack_unit like \'%' . $search . '%\'' .
                            ' or type.height like \'%' . $search . '%\'' .
                            ' or type.number_of_slots like \'%' . $search . '%\'' .
                            ' or type.terminating like \'' . $search . '%\'' .
                            ' or manu.manufacturer_name like \'%' . $search . '%\'' .
                            ' or plat.platform like \'%' . $search . '%\'' .                            
                            ' or type.part_number like \'%' . $search . '%\'' .
                            ' or shelf.serial_number like \'%' . $search . '%\'' .
                            ' order by shelf.shelf_name';
                } else {
                    $sql = 'select shelf.shelf_id, shelf.shelf_name, shelf.top_rack_unit, type.chassis_type, type.number_of_slots, type.height, type.terminating, manu.manufacturer_name, plat.platform, type.part_number, type.description, shelf.serial_number
                        from shelf as shelf left join chassis_type as type
                        on shelf.chassis_type_id = type.chassis_type_id
                        left join manufacturer as manu
                        on type.manufacturer_id = manu.manufacturer_id
                        left join platform as plat
                        on type.platform_id = plat.platform_id                          
                            order by shelf.shelf_name';
                }
                $q = $this->db->query($sql);
                //define $data array and set to null
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->shelf_id);                        
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {                                                                        
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data                        
                        }
                    }
                }
                if (empty($data['results'])) {
                    $data['extension'] = null;
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
                }
                $_SESSION['field'] = $type . '_name';
                return $data;
                break;
            case "slot":
                if ($hierarchy_flag != 'true') {
                    $sql = 'select slot.slot_id, slot.slot_number, slot.serial_number, slot.notes, type.slot_type, type.number_of_ports, manu.manufacturer_name, plat.platform, type.description
                        from slot as slot left join slot_type as type
                        on slot.slot_type_id = type.slot_type_id
                        left join manufacturer as manu 
                        on type.manufacturer_id = manu.manufacturer_id   
                        left join platform as plat
                        on type.platform_id = plat.platform_id                           
                        where slot.slot_number like \'%' . $search . '%\'' .
                            ' or slot.serial_number like \'%' . $search . '%\'' .
                            ' or slot.notes like \'%' . $search . '%\'' .
                            ' or type.slot_type like \'%' . $search . '%\'' .
                            ' or type.number_of_ports like \'%' . $search . '%\'' .
                            ' or manu.manufacturer_name like \'%' . $search . '%\'' .
                            ' or plat.platform like \'%' . $search . '%\'' .                            
                            ' or type.description like \'%' . $search . '%\'' .
                            ' order by CAST(slot.slot_number  AS SIGNED) ASC';
                } else {
                    $sql = 'select slot.slot_id, slot.slot_number, slot.serial_number, slot.notes, type.slot_type, type.number_of_ports, manu.manufacturer_name, plat.platform, type.description
                        from slot as slot left join slot_type as type
                        on slot.slot_type_id = type.slot_type_id
                        left join manufacturer as manu 
                        on type.manufacturer_id = manu.manufacturer_id   
                        left join platform as plat
                        on type.platform_id = plat.platform_id                           
                            order by CAST(slot.slot_number  AS SIGNED) ASC';
                }
                $q = $this->db->query($sql);
                //define $data array and set to null
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->slot_id);                         
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {                                                                                                
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data
                        }
                    }
                }
                if (empty($data['results'])) {
                    $data['extension'] = null;
                } else {
                    $data['extension'][0][0] = 'Slot Type';
                    $data['extension'][0][1] = 'slot_type';
                    $data['extension'][1][0] = 'Number Of Ports';
                    $data['extension'][1][1] = 'number_of_ports';
                    $data['extension'][2][0] = 'Manufacturer';
                    $data['extension'][2][1] = 'manufacturer_name';
                    $data['extension'][3][0] = 'Platform';
                    $data['extension'][3][1] = 'platform';                    
                    $data['extension'][4][0] = 'Description';
                    $data['extension'][4][1] = 'description';
                    $data['extension'][5][0] = 'Serial Number';
                    $data['extension'][5][1] = 'serial_number';
                    $data['extension'][6][0] = 'Notes';
                    $data['extension'][6][1] = 'notes';
                }
                $_SESSION['field'] = $type . '_number';
                return $data;
                break;
            case "sub_slot":
                if ($hierarchy_flag != 'true') {
                    $sql = 'select sub_slot.sub_slot_id, sub_slot.sub_slot_number, sub_slot.front_slot, sub_slot.serial_number, sub_slot.notes, type.sub_slot_type, type.number_of_ports, manu.manufacturer_name, plat.platform, type.description
                        from sub_slot as sub_slot left join sub_slot_type as type
                        on sub_slot.sub_slot_type_id = type.sub_slot_type_id
                        left join manufacturer as manu 
                        on type.manufacturer_id = manu.manufacturer_id   
                        left join platform as plat
                        on type.platform_id = plat.platform_id                            
                        where  sub_slot.sub_slot_number != \'0\' and
                        (sub_slot.sub_slot_number like \'%' . $search . '%\'' .
                            ' or sub_slot.serial_number like \'%' . $search . '%\'' .
                            ' or sub_slot.notes like \'%' . $search . '%\'' .
                            ' or type.sub_slot_type like \'%' . $search . '%\'' .
                            ' or type.number_of_ports like \'%' . $search . '%\'' .
                            ' or manu.manufacturer_name like \'%' . $search . '%\'' .
                            ' or plat.platform like \'%' . $search . '%\'' .                            
                            ' or type.description like \'%' . $search . '%\')' .
                            ' order by CAST(sub_slot.sub_slot_number  AS SIGNED) ASC';
                } else {
                    $sql = 'select sub_slot.sub_slot_id, sub_slot.sub_slot_number, sub_slot.front_slot, sub_slot.serial_number, sub_slot.notes, type.sub_slot_type, type.number_of_ports, manu.manufacturer_name, plat.platform, type.description
                        from sub_slot as sub_slot left join sub_slot_type as type
                        on sub_slot.sub_slot_type_id = type.sub_slot_type_id
                        left join manufacturer as manu 
                        on type.manufacturer_id = manu.manufacturer_id   
                        left join platform as plat
                        on type.platform_id = plat.platform_id                            
                        where  sub_slot.sub_slot_number != \'0\'
                            order by CAST(sub_slot.sub_slot_number  AS SIGNED) ASC';
                }
                $q = $this->db->query($sql);
                //define $data array and set to null
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->sub_slot_id);                         
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {                                                                                                                        
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data                       
                        }
                    }
                }
                if (empty($data['results'])) {
                    $data['extension'] = null;
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
                }
                $_SESSION['field'] = $type . '_number';
                return $data;
                break;
            case "port":
                if ($hierarchy_flag != 'true') {
                    $sql = 'select port.port_id, port.port_number, type.connector_type, media.media_type, circuit.circuit_type
                        from port as port left join connector_type as type
                        on port.connector_type_id = type.connector_type_id
                        left join media_type as media
                        on port.media_type_id = media.media_type_id
                        left join circuit_type as circuit
                        on port.circuit_type_id = circuit.circuit_type_id
                        where port.port_number like \'%' . $search . '%\'' .
                            ' or type.connector_type like \'%' . $search . '%\'' .
                            ' or media.media_type like \'%' . $search . '%\'' .
                            ' or circuit.circuit_type like \'%' . $search . '%\'' .
                            ' order by CAST(port.port_number AS SIGNED) ASC';
                } else {
                    $sql = 'select port.port_id, port.port_number, type.connector_type, media.media_type, circuit.circuit_type
                        from port as port left join connector_type as type
                        on port.connector_type_id = type.connector_type_id
                        left join media_type as media
                        on port.media_type_id = media.media_type_id
                        left join circuit_type as circuit
                        on port.circuit_type_id = circuit.circuit_type_id
                            order by CAST(port.port_number AS SIGNED) ASC';
                }
                $q = $this->db->query($sql);
                //define $data array and set to null
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->port_id);                          
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {                                                                                                                                                
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data                        
                        }
                    }
                }
                if (empty($data['results'])) {
                    $data['extension'] = null;
                } else {
                    $data['extension'][0][0] = 'Circuit Type';
                    $data['extension'][0][1] = 'circuit_type';                    
                    $data['extension'][1][0] = 'Connector Type';
                    $data['extension'][1][1] = 'connector_type';
                    $data['extension'][2][0] = 'Media Type';
                    $data['extension'][2][1] = 'media_type';                    
                }
                $_SESSION['field'] = $type . '_number';
                return $data;
                break;
                
            case "sub_port":
                if ($hierarchy_flag != 'true') {
                    $sql = 'select sub_port.sub_port_id, sub_port.sub_port_type
                    from sub_port as sub_port 
                    where sub_port.sub_port_type like \'%' . $search . '%\'' .
                            ' order by sub_port.sub_port_type DESC';
                } else {
                    $sql = 'select sub_port.sub_port_id, sub_port.sub_port_type
                    from sub_port as sub_port 
                        order by sub_port.sub_port_type DESC';
                }
                $q = $this->db->query($sql);
                //define $data array and set to null
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy($type, $row->sub_port_id);                        
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data                        
                        }
                    }
                }
                if (empty($data['results'])) {
                    $data['extension'] = null;
                }
                $_SESSION['field'] = $type . '_type';
                return $data;
                break;     
                
            case "connection":
                if ($hierarchy_flag != 'true') {
                    $sql = 'select sub_port.port_id, sub_port.sub_port_id, sub_port.connection_id
                    from sub_port as sub_port left join connection as connection
                    on sub_port.connection_id = connection.connection_id
                    where connection.connection_id like \'%' . $search . '%\'' .
                        'or connection.connection_id in 
                        (select ext.connection_id
                            from connection_external_relation as ext left join external_entity as ent
                            on ext.external_entity_id = ent.external_entity_id
                            where ent.external_entity_name like \'%' . $search . '%\'' .
                            ' or ext.external_order_id like \'%' . $search . '%\'' .
                            ' or ext.external_connection_id like \'%' . $search . '%\'' .
                            ' or ext.external_ticket_id like \'%' . $search . '%\'' .
                            ')' .
                            ' order by connection.connection_id';
                } else {
                    $sql = 'select sub_port.port_id, sub_port.sub_port_id, sub_port.connection_id
                            from connection as connection left join sub_port as sub_port
                            on sub_port.connection_id = connection.connection_id
                            order by connection.connection_id';
                }
 
                $q = $this->db->query($sql);
                $connection = null;                
                //define $data array and set to null
                $data = null;
                $connection_counter = 0;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) { 
                        $temp = $this->get_hierarchy('sub_port', $row->sub_port_id); //get hierarchy for port because each connection belongs to multiple ports                        
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {                        
                            $data['results'][] = $row; //store each row of search results into results array
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data 
                            if($data['hierarchy'][$connection_counter][0] != ''){ //if connection has a hierarchy, add connection to the end
                                $data['hierarchy'][$connection_counter][] = $row->connection_id; //add connection ID to end of hiearchy array
                                $data['links'][$connection_counter][] = base_url() . "inv/menu?type=connection&id=" . $row->connection_id . "&current=" . $row->connection_id;                                                  
                            }
                        
                        $connection = $row->connection_id;
                        //for each connection that matched the search, pull all external entity & ids 
                        $sql = 'select ent.external_entity_name, ext.external_order_id, ext.external_connection_id, ext.external_ticket_id
                            from connection_external_relation as ext left join external_entity as ent
                            on ext.external_entity_id = ent.external_entity_id
                            where ext.connection_id = ' .$connection .
                                ' order by ent.external_entity_name';
                        $subq = $this->db->query($sql);
                        $external_entity_counter = 0;
                        if ($subq->num_rows() > 0) {
                            foreach ($subq->result() as $subrow) { //store entity values into an array for each connection that holds an array for each external entity and ids
                                $data['extension_values'][$connection_counter][$external_entity_counter] = $subrow;
                                $external_entity_counter++;
                            }
                        }
                        $connection_counter++;
                        }
                    }//end foreach
                }//end if
                if (empty($data['results'])) {
                    $data['extension'] = null;
                } else {
                        $data['extension'][0][0] = 'External Entity';
                        $data['extension'][0][1] = 'external_entity_name';
                        $data['extension'][0][2] = 'External Order ID';
                        $data['extension'][0][3] = 'external_order_id';
                        $data['extension'][0][4] = 'External Connection ID';
                        $data['extension'][0][5] = 'external_connection_id';
                        $data['extension'][0][6] = 'External Ticket ID';
                        $data['extension'][0][7] = 'external_ticket_id';  
                }
                $_SESSION['field'] = $type . '_id';
                return $data;
                break;   
                
            case "circuit":
                if ($hierarchy_flag != 'true') {                
                $sql = 'select circuit.circuit_id, circuit.peerless_order_id, shelf.shelf_id, shelf.shelf_name
                    from circuit as circuit 
                    left join circuit_shelf as cs
                    on circuit.circuit_id = cs.circuit_id
                    left join shelf as shelf
                    on cs.shelf_id = shelf.shelf_id
                    where circuit.peerless_order_id like \'%' . $search . '%\'' .
                    ' or shelf.shelf_name like \'%' . $search . '%\'' .
                    ' order by circuit.peerless_order_id';
                }else{
                    $sql = 'select circuit.circuit_id, circuit.peerless_order_id, shelf.shelf_id, shelf.shelf_name
                        from circuit as circuit 
                        left join circuit_shelf as cs
                        on circuit.circuit_id = cs.circuit_id
                        left join shelf as shelf
                        on cs.shelf_id = shelf.shelf_id
                            order by circuit.peerless_order_id';                    
                }

                $q = $this->db->query($sql);
                //define $data array and set to null
                $data = null;
                if ($q->num_rows() > 0) {
                    foreach ($q->result() as $row) {
                        $temp = $this->get_hierarchy('shelf', $row->shelf_id);                          
                        if (($hierarchy_flag == 'true' && preg_grep('~' . strtolower($search) . '~', array_map('strtolower', $temp['list']))) || $hierarchy_flag != 'true') {                        
                            $data['results'][] = $row;
                            $data['hierarchy'][] = $temp['list']; //get hierarchy list from temp return data
                            $data['links'][] = $temp['links']; //get links for hierarchy from temp return data  
                        }
                    }
                }
                $data['extension'] = null;

                $_SESSION['field'] = 'peerless_order_id';
                return $data;
                break;                
        }
    }
    
    /**********************************************************
     * Insert & Update Functions
     *********************************************************/
    function update_sub_ports_for_connection(){
        $transmit_port_id = $this->input->post('transmit_port_id');
        $receive_port_id = $this->input->post('receive_port_id');
        $connection_id = $this->input->post('connection_id');
        
        //remove transport connection if connection assigned to another port
        $this->db->set('connection_id', null);
        $this->db->where('connection_id', $connection_id);
        $this->db->where('sub_port_type', 'Transmit');
        $this->db->where('port_id !=', $transmit_port_id);
        $this->db->update('sub_port');
        //remove receive connection if connection assigned to another port
        $this->db->set('connection_id', null);
        $this->db->where('connection_id', $connection_id);
        $this->db->where('sub_port_type', 'Receive');
        $this->db->where('port_id !=', $receive_port_id);        
        $this->db->update('sub_port');

        //update transmit subport
        $this->db->set('connection_id', $connection_id);
        $this->db->where('port_id', $transmit_port_id);
        $this->db->where('sub_port_type', 'Transmit');      
        if(!$this->db->update('sub_port')){
            $_SESSION['error_message'] = 'Error updating Transmit Port!';
        }    
        
        //udpate receive subport
        $this->db->set('connection_id', $connection_id);
        $this->db->where('port_id', $receive_port_id);
        $this->db->where('sub_port_type', 'Receive');   
        if(!$this->db->update('sub_port')){
            $_SESSION['error_message'] = 'Error updating Receive Port!';
        }     
    }
    
    function update_shelves_for_circuit($primary_key){
        $circuit_id = $primary_key;
        $a_end_shelf_id = $this->input->post('a_end_shelf_id');
        $z_end_shelf_id = $this->input->post('z_end_shelf_id');
        $connections = $this->input->post('connections');
        
        //remove old A-End and Z-End shelves, if they already exist for circuit
//        $this->db->set('shelf_id', null);
//        $this->db->where('circuit_id', $circuit_id);
//        $this->db->where('termination_type', 'A-End');
//        $this->db->where('shelf_id !=', $a_end_shelf_id);
//        $this->db->update('circuit_shelf');
//
//        $this->db->set('shelf_id', null);
//        $this->db->where('circuit_id', $circuit_id);
//        $this->db->where('termination_type', 'Z-End');
//        $this->db->where('shelf_id !=', $z_end_shelf_id);
//        $this->db->update('circuit_shelf');
          
        $this->db->where('circuit_id', $circuit_id);
        $this->db->where('termination_type', 'A-End');
        $query = $this->db->get('circuit_shelf');
        if ($query->num_rows() > 0){ //if record exists for A-End of circuit, update the record
            $this->db->set('shelf_id', $a_end_shelf_id);
            $this->db->where('circuit_id', $circuit_id);
            $this->db->where('termination_type', 'A-End');      
            if(!$this->db->update('circuit_shelf')){
                $_SESSION['error_message'] = 'Error updating A-End Shelf!';
            }  
        }
        else{ //if record does not exist, create new record for circuit with A-End shelf
            $this->db->set('circuit_id', $circuit_id);
            $this->db->set('termination_type', 'A-End');
            $this->db->set('shelf_id', $a_end_shelf_id);
            $this->db->insert('circuit_shelf');   
        }
 
        $this->db->where('circuit_id', $circuit_id);
        $this->db->where('termination_type', 'Z-End');
        $query = $this->db->get('circuit_shelf');
        if ($query->num_rows() > 0){ //if record exists for Z-End of circuit, update the record
            $this->db->set('shelf_id', $z_end_shelf_id);
            $this->db->where('circuit_id', $circuit_id);
            $this->db->where('termination_type', 'Z-End');      
            if(!$this->db->update('circuit_shelf')){
                $_SESSION['error_message'] = 'Error updating Z-End Shelf!';
            }  
        }
        else{ //if record does not exist, create new record for circuit with Z-End shelf
            $this->db->set('circuit_id', $circuit_id);
            $this->db->set('termination_type', 'Z-End');
            $this->db->set('shelf_id', $z_end_shelf_id);
            $this->db->insert('circuit_shelf');   
        }           
    }
    
    function create_ports_for_new_slot($slot_type_id, $primary_key) {

        $this->db->select('slot_type, number_of_ports, circuit_type_id, media_type_id, connector_type_id, number_of_sub_slots');
        $this->db->where('slot_type_id', $slot_type_id);
        $q = $this->db->get('slot_type');
        $sub_slot_type_id = null;
        $circuit_type_id = null;
        $media_type_id = null;
        $connector_type_id = null;
        $number_of_ports = null;
        $number_of_sub_slots = null;
        $slot_type = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $circuit_type_id = $row->circuit_type_id;
                $media_type_id = $row->media_type_id;
                $connector_type_id = $row->connector_type_id;
                $number_of_ports = $row->number_of_ports;
                $number_of_sub_slots = $row->number_of_sub_slots;
                $slot_type = $row->slot_type;
            }
        }


        if ($number_of_sub_slots == 0) { //has zero sub slots so create all ports
            //get dummy sub slot type id of N/A
            $this->db->select('sub_slot_type_id');
            $this->db->where('sub_slot_type', 'N/A');
            $q = $this->db->get('sub_slot_type');
            if ($q->num_rows() > 0) {
                foreach ($q->result() as $row) {
                    $sub_slot_type_id = $row->sub_slot_type_id;
                }
            }

            //create subslot of type N/A and sub slot number 0
            $this->db->set('slot_id', $primary_key);
            $this->db->set('sub_slot_number', '0');
            $this->db->set('sub_slot_type_id', $sub_slot_type_id);
            if ($this->db->insert('sub_slot')) {
                $sub_slot_id = $this->db->insert_id();
                //create ports
                if (isset($circuit_type_id) && isset($media_type_id) && isset($connector_type_id) && isset($number_of_ports)) {
                    for ($i = 1; $i <= $number_of_ports; $i++) {
                        $this->db->set('port_number', $i);
                        $this->db->set('sub_slot_id', $sub_slot_id);
                        $this->db->set('circuit_type_id', $circuit_type_id);
                        $this->db->set('media_type_id', $media_type_id);
                        $this->db->set('connector_type_id', $connector_type_id);
                        $this->db->insert('port');

                        $pos = stripos($slot_type, 'LCX');
                        if ($pos === false) { //if slot is not an LCX panel, create normal ports with both transmit & receive sub ports                             
                            //create Transmit sub_port
                            $new_port_id = $this->db->insert_id();
                            $this->db->set('port_id', $new_port_id);
                            $this->db->set('sub_port_type', 'Transmit');
                            $this->db->insert('sub_port');
                            //create Receive sub_port
                            $this->db->set('port_id', $new_port_id);
                            $this->db->set('sub_port_type', 'Receive');
                            $this->db->insert('sub_port');
                        }
                    }
                }
            }
        }
    }

    function delete_all_ports_for_slot($primary_key){
        $this->db->select('sub_slot_id');
        $this->db->where('slot_id', $primary_key);
        $q = $this->db->get('sub_slot');
        $sub_slot_id = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $sub_slot_id = $row->sub_slot_id;
            }
        } 
        if(isset($sub_slot_id)){
            //delete all connections with before deleting sub ports
            $sql = 'DELETE connection FROM connection as connection
                JOIN sub_port as sub_port ON sub_port.connection_id = connection.connection_id
                JOIN port as port ON sub_port.port_id = port.port_id
                WHERE port.sub_slot_id = ' . $sub_slot_id;
            if(!$this->db->query($sql)){
                $_SESSION['error_message'] = 'Error deleting connections for slot id ' . $primary_key;
            }            
            //delete all sub_ports with sub_slot_id
            $sql = 'DELETE sub_port FROM sub_port as sub_port
                JOIN port as port ON sub_port.port_id = port.port_id
                WHERE port.sub_slot_id = ' . $sub_slot_id;
            if(!$this->db->query($sql)){
                $_SESSION['error_message'] = 'Error deleting sub_ports';
            }
            //delete all ports with sub_slot_id
            $this->db->where('sub_slot_id', $sub_slot_id);
            $this->db->delete('port'); 
            //delete sub_slot
            $this->db->where('sub_slot_id', $sub_slot_id);
            $this->db->delete('sub_slot');
        }
    }

    function create_sub_ports_for_new_port($primary_key) {
        //get chassis type to check if LCX panel or not
        $this->db->select('slot_type.slot_type');
        $this->db->join('sub_slot', 'port.sub_slot_id = sub_slot.sub_slot_id');
        $this->db->join('slot', 'sub_slot.slot_id = slot.slot_id');
        $this->db->join('slot_type', 'slot.slot_type_id = slot_type.slot_type_id');
        $this->db->where('port_id', $primary_key);
        $q = $this->db->get('port');
        $slot_type = null;
        if ($q->num_rows() > 0) {
            foreach ($q->result() as $row) {
                $slot_type = $row->slot_type;
            }
        }        
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
    
    function delete_connection_from_port($primary_key){
        if(isset($primary_key)){
            //delete all connections with before deleting sub ports
            $sql = 'DELETE connection FROM connection as connection
                JOIN sub_port as sub_port ON sub_port.connection_id = connection.connection_id
                JOIN port as port ON sub_port.port_id = port.port_id
                WHERE port.port_id = ' . $primary_key;
            if(!$this->db->query($sql)){
                $_SESSION['error_message'] = 'Error deleting connections for slot id ' . $primary_key;
            }            
        }
    }    

}

//End of inv_model.php
//Location: application\models\inv_model.php