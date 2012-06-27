<?php
    error_reporting(E_ALL);
class Site_model extends CI_Model{

    function getAll(){
       
        $q = $this->db->get('country');

        if($q->num_rows() > 0){
            foreach ($q->result() as $row){
                $data[] = $row;
            }
        }
        
        return $data;
    }
    
    function getCity(){
        $q = $this->db->get('city');
        
        
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $data[]=$row;
            }
                
            }
        return $data;
    }
    
    function getBuilding(){
        $q = $this->db->get('building');
        
        if($q->num_rows() > 0){
            foreach($q->result() as $row){
                $data[]=$row;
            }
        }
        return $data;
    }
}

?>