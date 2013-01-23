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
        var base_url = '<?php echo base_url(); ?>';
        var previous_url = '<?php if (sizeof($_SESSION["previouslevelink"])>0) { echo $_SESSION["previouslevelink"][sizeof($_SESSION["previouslevelink"])-2];} else {echo base_url();} ?>';</script>
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
                    echo '<h2 class="heading">' . ucwords(preg_replace('/_/', ' ', $_SESSION['type'][sizeof($_SESSION['type']) - 1])) . ' ' . $connection_id. '</h2>';
                    if($this->session->userdata('is_logged_in')){ 
                        echo '&nbsp; <a class="crud" href="' . base_url() . 'admin/connection_external_relation/add">add external relation</a>';    
                    }?>
            
            <hr class="list">
                <table id="currentlist">
                    <?php if(sizeof($sub_port)!==0){
                                $display = $sub_port[0]->connection_id;
                                $id_number = $sub_port[0]->connection_id; ?>
                    <tr>
                        <?php 
                            if($type == 'connection'){ //add port_id to link if type is connection to track which port connection is associated with because connections are associated with multiple ports ?>
                                <td><a class="list" id="<?php echo $id_number; ?>" href="<?php echo base_url(); ?>inv/menu?type=<?php echo $type; ?>&id=<?php echo $id_number; ?>&current=<?php echo $id_number ?>&port=<?php echo $sub_port[0]->port_id ?>"><?php echo $display; ?></a></td>
                        <?php }
                            if($this->session->userdata('is_logged_in')){ //print crud links if logged in ?>
                            <td><a class="crud" href="<?php echo base_url(); ?>admin/<?php echo $type; ?>">view</a></td>
                            <td><a class="crud" href="<?php echo base_url(); ?>admin/<?php echo $type; ?>/edit/<?php echo $sub_port[0]->connection_id; ?>">edit</a></td>
                            <td><a class="crud delete-link" href="<?php echo base_url(); ?>admin/<?php echo $type; ?>/delete/<?php echo $id_number; ?>">delete</a></td>
                    <?php } 
                        //begin extension (pop up) data
                        if (isset($extension[0])) { //check if any extension data is present
                            $tooltipCreated = false; //flag to indicate when to create tooltip div
                            if($type == 'connection' && isset($extension_values)){ //if type is connection type and external ids present, loop through array of external entity data & external id's
                              $i = 1; //initialize counter for extension data
                              foreach ($extension_values as $row): 
                                    $ext = $extension[0][$i]; //temporary variable to hold extension field name
                                    while($i < sizeof($extension[0])){
                                        if(isset($row->$ext) && (string)$row->$ext !== '') {
                                            //check if tooltip div created, if not create it
                                            if($tooltipCreated === false){ ?>
                                                <div class="tooltip <?php echo $id_number ?>">
                                                <?php $tooltipCreated = true;
                                            } 
                                            if($i <= 1){ $class = 'entity-name';} //add class for entity title or entity property
                                            else{ $class = 'entity-id';} ?>
                                            <p><span class="label <?php echo $class; ?>"><?php echo $extension[0][$i-1] . ': '?></span><?php echo $row->$ext;?></p>
                                            <?php 
                                            $i +=2;
                                            if($i < sizeof($extension[0])){
                                                $ext = $extension[0][$i];
                                            }
                                        }else{
                                            $i +=2;
                                            if($i < sizeof($extension[0])){
                                                $ext = $extension[0][$i];
                                            }                                            
                                        }
                                     }
                                     $i = 1; //re-initialize counter for next set of extension data
                                endforeach; } //end if connection
                            if($tooltipCreated){ //close div tag if created?> 
                                </div>
                            <?php }//end if
                        }//end if extension data present ?>
                    </tr>
                        <?php }else{?>
                        <tr class="empty">
                            <td>None
                                <?php if($this->session->userdata('is_logged_in')){ //print crud links if logged in ?>
                                    &nbsp;<a class="crud" href="<?php echo base_url() ?>admin/<?php echo $type; ?>/add">add</a>
                                <?php } ?>
                        </td></tr>
                    <?php }//end if ?>
                </table>
            </div>        

                <table id="currentlist" class="tablesorter">
                <thead><tr><th class="top_header">Type</th><th colspan="8" class="top_header">Hierarchy</th></tr>
                    <tr><th class="hierarchy-list">Sub Port Type</th> 
                        <?php for($i=3;$i<11;$i++){ 
                                echo '<th class="hierarchy-list">' . $table_headers[$i] . '</th>';
                        } ?>
                </thead>
                <tbody>
                    <?php if(sizeof($sub_port)!==0){
                            foreach ($sub_port as $row):
                                $display = $row->$field_name;
                                $id_number = $row->$id; ?>                                
                    <tr>
                        <?php 
                              if($type == 'connection' && isset($row->connection_id)){ //ensure type is connection and that current row has data?>                    
                                <td><a class="list float" id="<?php echo $row->sub_port_id; ?>" href="<?php echo base_url(); ?>inv/menu?type=sub_port&id=<?php echo $row->sub_port_id; ?>&current=<?php echo $row->sub_port_type ?>"><?php echo $row->sub_port_type . ($row->sub_port_type == 'Transmit'? ' ►': ' ◄'); ?></a>  
                                <?php if($this->session->userdata('is_logged_in')){ //display crud links if logged in ?>
                                    <span class="circuit-crud">
                                    <a class="crud" href="<?php echo base_url(); ?>admin/connection/edit/<?php echo $row->connection_id; ?>">edit</a></td>
                                    </span><span class="clear"></span>
                                <?php } ?>
                                </td>
                                <?php 
                                $current_hierarchy = ${strtolower($row->sub_port_type . '_hierarchy')};
                                $current_links = ${strtolower($row->sub_port_type . '_links')};
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
                    }else{?>
                        <tr class="empty">
                            <td>None &nbsp;
                                <?php if($this->session->userdata('is_logged_in')){ //print crud links if logged in ?>
                                    &nbsp;<a class="crud" href="<?php echo base_url() ?>admin/<?php echo $type . '/edit/' . $connection_id; ?>">edit</a>
                                <?php } ?>
                        </td></tr>
                    <?php }//end if ?>
                </tbody>
                </table>
                <table id="currentlist" class="tablesorter">
                <thead><tr><th class="top_header">Circuit</th></tr>
                    
                </thead>
                <tbody>                    
                <?php if($type == 'connection' && sizeof($circuit_id) > 0){ ?>    
                    <?php for($i=0;$i < sizeof($circuit_id); $i++){ ?>
                           <?php if(isset($peerless_order_id[$i])){ ?> 
                                <tr><td><a class="list float" id="<?php echo $circuit_id[$i]; ?>" href="<?php echo base_url(); ?>inv/menu?type=circuit&id=<?php echo $circuit_id[$i]; ?>&current=<?php echo $peerless_order_id[$i] ?>"><?php echo $peerless_order_id[$i]; ?></a>  
                                <?php if($this->session->userdata('is_logged_in')){ //display crud links if logged in ?>
                                    <span class="circuit-crud">
                                    <a class="crud" href="<?php echo base_url(); ?>admin/connection/edit/<?php echo $connection_id; ?>">edit</a>
                                    <a class="crud delete-link" href="<?php echo base_url(); ?>admin/circuit_connection/delete/<?php echo $circuit_connection_id[$i]; ?>">delete</a>
                                    </span><span class="clear"></span>
                                <?php }//end if logged in ?> 
                                </td></tr>    
                        <?php   }
                      }//end foreach 
                }else{?>
                        <tr class="empty">
                            <td>None
                                <?php if($this->session->userdata('is_logged_in')){ //print crud links if logged in ?>
                                    &nbsp;<a class="crud" <?php echo 'href="' . base_url() . 'admin/connection/edit/' . $connection_id . '">'; ?>add</a>
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
