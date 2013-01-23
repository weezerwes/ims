<?php ?>

<!DOCTYPE html>

<html>
    <head>
        <title><?php echo ($this->session->userdata('is_logged_in') ? "Inventory Management - Peerless Network" : "Inventory View - Peerless Network") ?></title>
        <meta http-equiv="Content-Type" content ="text/html; charset=UTF-8">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/inventory.css" type="text/css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/blue/style.css" type="text/css">        
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>assets/grocery_crud/themes/flexigrid/js/cookies.js"></script>
        <script src="<?php echo base_url(); ?>assets/grocery_crud/themes/flexigrid/js/jquery.form.js"></script>
        <script src="<?php echo base_url(); ?>assets/grocery_crud/themes/flexigrid/js/jquery.numeric.js"></script>
        <script src="<?php echo base_url(); ?>assets/grocery_crud/themes/flexigrid/js/jquery.printElement.min.js"></script>
        <script>var current_url = '<?php if (sizeof($_SESSION["previouslevelink"])>0) { echo $_SESSION["previouslevelink"][sizeof($_SESSION["previouslevelink"])-1];} else {echo base_url();} ?>';
        var base_url = '<?php echo base_url(); ?>';</script>
        <script src="<?php echo base_url(); ?>js/jquery-ui-1.8.24.custom.min.js"></script>   
        <script src="<?php echo base_url(); ?>js/jquery.tablesorter.min.js"></script>                
        <script src="<?php echo base_url(); ?>js/script.js"></script>
    </head>

    <body>
      <div id="maincontent">        
        <!--import logo login box-->
        <?php if($this->session->userdata('is_logged_in')){
            include("_header_admin.php"); 
        }else{
            include("_header.php");
        }?> 
        <!--import breadcrumbs-->
        <?php include("_breadcrumbs.php"); ?>
        <?php include("_main_menu.php"); ?>
            <div id="menu">  
            <div id="hidden-operations"></div>
            <div id='report-error' class='report-div error'>
                <?php if(isset($_SESSION['error_message'])){
                    echo $_SESSION['error_message'];
                    $_SESSION['error_message'] = null;
                    } ?>
            </div>
            <div id='report-success' class='report-div success report-list'></div>  
                <?php 
                    $id = $_SESSION['type'][sizeof($_SESSION['type']) - 1] . "_id";
                    $type = $_SESSION['type'][sizeof($_SESSION['type']) - 1];
                    $counter = 0;
                    $table_headers = array('Country', 'City', 'Building', 'Location', 'Aisle', 'Bay', 'Shelf', 'Slot', 'Sub Slot', 'Port', 'Sub Port', 'Connection');
                    echo '<h2 class="heading">' . ucwords(preg_replace('/_/', ' ', $_SESSION['type'][sizeof($_SESSION['type']) - 1])) . ' ' . $peerless_order_id . '</h2>';
                    echo '&nbsp; <a class="crud" target="_blank" href="http://oms.prlss.net/orderJump.php?order_id=' . $peerless_order_id . '">view in oms</a>';
                    if($this->session->userdata('is_logged_in')){ 
                        echo '&nbsp<a class="crud" href="' . base_url() . 'admin/circuit/edit/' . $circuit_id . '">add connections</a>&nbsp<a class="crud" href="' . base_url() . 'admin/circuit/edit/' . $circuit_id . '">edit</a>&nbsp;<a class="crud delete-link" href="' . base_url() . 'admin/circuit/delete/' . $circuit_id . '">delete</a>';    
                    }?>
            <hr class="list">
                <table id="currentlist" class="tablesorter">
                <thead><tr><th class="top_header">Terminations</th><th colspan="12" class="top_header">Hierarchy</th></tr>
                    <tr><th class="hierarchy-list"><?php echo 'Shelf' ?></th><th class="hierarchy-list">Country</th><th class="hierarchy-list">City</th><th class="hierarchy-list">Building</th><th class="hierarchy-list">Location</th><th class="hierarchy-list">Aisle</th><th class="hierarchy-list">Bay</th><th class="hierarchy-list">Shelf</th></tr>
                </thead>
                <tbody>
                    <?php if(sizeof($$type)!==0){
                            foreach ($$type as $row):
                                $display = $row->$field_name;
                                $id_number = $row->$id; ?>                                
                    <tr>
                        <?php 
                              if(($type == 'circuit' && isset($row->shelf_id))){ //if more data than just circuit data, display shelves and connections for circuit ?>
                              <?php if(isset($row->shelf_id)){ ?>                      
                                <td><a class="list float" id="<?php echo $row->shelf_id; ?>" href="<?php echo base_url(); ?>inv/menu?type=shelf&id=<?php echo $row->shelf_id; ?>&current=<?php echo $row->shelf_name ?>"><?php echo $row->termination_type . ': ' . $row->shelf_name; ?></a>  
                                <?php if($this->session->userdata('is_logged_in')){ //display crud links if logged in ?>
                                    <span class="circuit-crud">
                                    <a class="crud" href="<?php echo base_url(); ?>admin/circuit_shelf">view</a>
                                    <a class="crud" href="<?php echo base_url(); ?>admin/circuit_shelf/edit/<?php echo $row->circuit_shelf_id; ?>">edit</a>
                                    <a class="crud delete-link" href="<?php echo base_url(); ?>admin/circuit_shelf/delete/<?php echo $row->circuit_shelf_id; ?>">delete</a>
                                    </span><span class="clear"></span>
                                <?php } ?>
                                </td>
                                <?php for($i=0;$i<sizeof($hierarchy[$counter]); $i++){
                                    echo '<td class="hierarchy-list circuit-hierarchy"><a href="' . $links[$counter][$i] . '">' . $hierarchy[$counter][$i] . '</a></td>';
                                      } 
                                   }
                              }?>
                    </tr>
                        <?php $counter++;
                        endforeach;
                    }else{?>
                        <tr class="empty">
                            <td>None
                                <?php if($this->session->userdata('is_logged_in')){ //print crud links if logged in ?>
                                    &nbsp;<a class="crud" href="<?php echo base_url() ?>admin/<?php echo $type; ?>/add">add</a>
                                <?php } ?>
                        </td></tr>
                    <?php }//end if ?>
                </tbody>
                </table>
            
                <table id="currentlist" class="tablesorter">
                <thead><tr><th class="top_header">Connections</th><th class="top_header">Type</th><th colspan="8" class="top_header">Hierarchy</th></tr>
                    <tr><th class="hierarchy-list">Connection ID</th><th class="hierarchy-list">Sub Port Type</th> 
                        <?php for($i=3;$i<11;$i++){ 
                                echo '<th class="hierarchy-list">' . $table_headers[$i] . '</th>';
                        } ?>
                </thead>
                <tbody>
                    <?php 
                    if(isset($sub_port)){ //if no connections (subport array is null) skip this section
                        for($c=0;$c<sizeof($sub_port);$c++){ //for each connection - subport is an array of arrays of pairs of subports
                            if(sizeof($sub_port[$c])!==0){ 
                                foreach ($sub_port[$c] as $row): //for each pair of subports
                                    $display = $row->connection_id;
                                    $id_number = $row->connection_id; ?>                                
                    <tr>
                        <?php 
                              if(isset($row->connection_id)){ //ensure current row has data?>  
                                <td><a class="list float" id="<?php echo $id_number; ?>" href="<?php echo base_url(); ?>inv/menu?type=connection&id=<?php echo $id_number; ?>&current=<?php echo $id_number; ?>"><?php echo $id_number; ?></a>  
                                <?php if($this->session->userdata('is_logged_in')){ //display crud links if logged in ?>
                                    <span class="circuit-crud">
                                    <a class="crud delete-link" href="<?php echo base_url(); ?>admin/circuit_connection/delete/<?php echo $circuit_connection_id[$c]; ?>">delete</a>                                    
                                    </span><span class="clear"></span>
                                <?php }//end if logged in ?> 
                                </td>
                                <td><a class="list float" id="<?php echo $row->sub_port_id; ?>" href="<?php echo base_url(); ?>inv/menu?type=sub_port&id=<?php echo $row->sub_port_id; ?>&current=<?php echo $row->sub_port_type ?>"><?php echo $row->sub_port_type . ($row->sub_port_type == 'Transmit'? ' ►': ' ◄'); ?></a>  
                                <?php if($this->session->userdata('is_logged_in')){ //display crud links if logged in ?>
                                    <span class="circuit-crud">
                                    <a class="crud" href="<?php echo base_url(); ?>admin/connection/edit/<?php echo $row->connection_id; ?>">edit</a></td>
                                    </span><span class="clear"></span>
                                <?php } ?>
                                </td>
                                <?php 
                                $current_hierarchy = ${strtolower($row->sub_port_type . '_hierarchy')}[$c];
                                $current_links = ${strtolower($row->sub_port_type . '_links')}[$c];
                                for($i=3;$i<sizeof($current_hierarchy); $i++){
                                    if($current_hierarchy[$i] != 'Sub Slot 0'){
                                        echo '<td class="hierarchy-list circuit-hierarchy"><a href="' . $current_links[$i] . '">' . $current_hierarchy[$i] . '</a></td>';
                                    }else{
                                        echo '<td class="hierarchy-list"></td>';
                                    }                                     
                                } 
                                $counter++;
                              }?>
                    </tr>
                        <?php endforeach;
                        if($counter < 2){ 
                            echo '<tr><td><a class="crud" href="' . base_url() . 'admin/sub_port/add/">add other end of connection</a></td></tr>';
                        }
                    }
                    }//end for
                    }else{?>
                        <tr class="empty">
                            <td>None &nbsp;
                                <?php if($this->session->userdata('is_logged_in')){ //print crud links if logged in ?>
                                    &nbsp;<a class="crud" href="<?php echo base_url() ?>admin/<?php echo $type . '/edit/' . $circuit_id; ?>">add connection</a>
                                <?php } ?>
                        </td></tr>
                    <?php }//end if ?>
                </tbody>
                </table>
            
            
            </div>

            <p><a href="<?php echo base_url(); ?>">↩ Start Over</a> | <a href="<?php echo base_url() . 'search'; ?>">Search</a></p>
            <?php if($this->session->userdata('is_logged_in')){ //if logged in, display edit tables link ?>
                <p><a class="crud" href="<?php echo base_url() . 'admin/edit'; ?>">Edit All Tables</a></p>
            <?php } ?>
        </div>
    </body>
</html>
