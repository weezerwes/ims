<?php

class Circuit_model extends grocery_CRUD_Model  {
 
    function get_relation_n_n_unselected_array($field_info, $selected_values)
 
    {
        $selection_primary_key = $this->get_primary_key($field_info->selection_table);
 
        if($field_info->field_name == 'connections')
 
        {
            $this->db->select('connection.connection_id');
            $this->db->join('circuit_connection', 'connection.connection_id = circuit_connection.connection_id', 'left');
            $this->db->where(array('circuit_connection.connection_id' => NULL));
        }
 
        $this->db->order_by("{$field_info->selection_table}.{$field_info->title_field_selection_table}");
 
        $results = $this->db->get($field_info->selection_table)->result();
 
 
        $results_array = array();
        foreach($results as $row)
 
        {
            if(!isset($selected_values[$row->{$field_info->primary_key_alias_to_selection_table}]))
                $results_array[$row->{$field_info->primary_key_alias_to_selection_table}] = $row->{$field_info->title_field_selection_table}; 
        }
 
 
        return $results_array;        
    }
 
}