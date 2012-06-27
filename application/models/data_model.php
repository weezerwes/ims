<?php

class Data_model extends CI_Model{
//    
//    function getAll(){
//        $q = $this->db->query("select * from data;");
//        
//        if($q->num_rows() > 0){
//            foreach($q->result() as $row){
//                $data[] = $row;
//            }
//            
//        return $data;
//        }
//    }
    //uses active records
    function getAll(){
        $q = $this->db->get('data');
        
        if($q->num_rows() > 0) {
            foreach($q->result() as $row){
                $data[] = $row;
            }
            return $data;
        }
    }
    
//    function getMost(){
//        $this->db->select('title, contents, author');
//        $q = $this->db->get('data');
//        
//        if($q->num_rows() > 0) {
//            foreach($q->result() as $row){
//                $data[] = $row;
//            }
//            return $data;
//        }
//    }
    //sql binding
    function getAuthor(){
        $sql = "select title, author, contents from data where id = ? and author = ?";
        $q = $this->db->query($sql, array(2, 'John Doe'));
        echo $q->num_rows();
        if($q->num_rows() > 0) {
            foreach($q->result() as $row){
                $data[] = $row;
            }
            return $data;
        }
    }
    
    function getMost(){
        $this->db->select('title, contents, author');
        $this->db->from('data');
        $this->db->where('author', 'Wesley Stephens');
        $q = $this->db->get();
        
        echo $q->num_rows();
        if($q->num_rows() > 0) {
            foreach($q->result() as $row){
                $data[] = $row;
            }
            return $data;
        }
        
    }
}

?>