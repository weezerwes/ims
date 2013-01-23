<?php ?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo "Inventory View - Peerless Network" ?></title>
        <meta http-equiv="Content-Type" content ="text/html; charset=UTF-8">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/blue/style.css" type="text/css">        
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/inventory.css" type="text/css">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/blue/style.css" type="text/css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
        <script src="<?php echo base_url(); ?>assets/grocery_crud/themes/flexigrid/js/cookies.js"></script>
        <script src="<?php echo base_url(); ?>assets/grocery_crud/themes/flexigrid/js/jquery.form.js"></script>
        <script src="<?php echo base_url(); ?>assets/grocery_crud/themes/flexigrid/js/jquery.numeric.js"></script>
        <script src="<?php echo base_url(); ?>assets/grocery_crud/themes/flexigrid/js/jquery.printElement.min.js"></script>
        <script src="<?php echo base_url(); ?>js/jquery.tablesorter.min.js"></script>        
        <script>var current_url = '<?php if (sizeof($_SESSION["previouslevelink"])>0) { echo $_SESSION["previouslevelink"][sizeof($_SESSION["previouslevelink"])-1];} else {echo base_url();} ?>';
        var base_url = '<?php echo base_url(); ?>';</script>
        <script src="<?php echo base_url(); ?>js/script.js"></script>
    </head>

    <body>
      <div id="maincontent">        
        <!--import header-->
        <?php if($this->session->userdata('is_logged_in')){
                include("_header_admin.php");
              }else{
                include("_header.php");
              }?> 
        <!--import breadcrumbs-->
        <?php include("_no_breadcrumbs.php"); ?>
        <?php include("_main_menu.php"); ?>
        <h2 class="heading">Search</h2>
        <?php echo form_open('search/index', array('class' => 'search_form forms')); ?>
            <?php $types = array('aisle' => 'Aisle', 'bay' => 'Bay', 'building' => 'Building', 'circuit' => 'Circuit', 'connection' => 'Connection', 'city' => 'City', 'location' => 'Location', 'port' => 'Port', 'shelf' => 'Shelf', 'slot' => 'Slot', 'sub_port' => 'Sub Port', 'sub_slot' => 'Sub Slot'); 
                  $table_headers = array('Country', 'City', 'Building', 'Location', 'Aisle', 'Bay', 'Shelf', 'Slot', 'Sub Slot', 'Port', 'Sub Port', 'Connection');
            ?>            
            <?php echo form_dropdown('type', $types); ?>
            <?php echo form_input(array('name' => 'query', 'class' => 'form-input-box', 'id' => 'search_field')); ?>
            <?php echo form_submit('search', 'Search'); ?>
            <?php echo form_checkbox('hierarchy_flag', 'true', FALSE, 'id="hierarchy_flag" class="search-checkbox"') . '<label for="hierarchy_flag" class="search-checkbox-text">Search Within Hierarchy</label>'; ?>
        <?php echo form_close(); 
        
        //if search resutls are present...
         if ( isset($results)){ 
                echo '<h2 class="heading">' . ucwords(preg_replace('/_/', ' ', $type)) . ' results for query \'' . $_POST['query'] . '\' </h2>'; ?>
            <hr class="list">
            <table id="currentlist" class="tablesorter">
                <thead><tr><th class="top_header">Results</th><th colspan="12" class="top_header">Hierarchy</th></tr>
                    <tr><th class="hierarchy-list"><?php echo ucwords($type) ?></th>
                        <?php for($i=0;$i<sizeof($hierarchy[0]);$i++){ 
                            echo '<th class="hierarchy-list">' . $table_headers[$i] . '</th>';
                              } ?>
                    </tr></thead>
                <tbody>
            <?php $field_name = $_SESSION['field'];
                  $id = $type . '_id';
                  $counter = 0;
               foreach($results as $row):
                   $display = $row->$field_name; 
                   $id_number = $row->$id;?>
                    <tr><?php if($type == 'connection'){ //add port_id to link if type is connection to track which port connection is associated with because connections are associated with multiple ports ?>
                            <td><a class="list" id="<?php echo $row->$id; ?>" href="<?php echo base_url(); ?>inv/menu?type=<?php echo $type; ?>&id=<?php echo $row->$id; ?>&current=<?php echo $row->$field_name ?>&port=<?php echo $row->port_id ?>"><?php echo $display; ?></a></td>
                        <?php }else{ ?>
                            <td><a class="list" id="<?php echo $row->$id; ?>" href="<?php echo base_url(); ?>inv/menu?type=<?php echo $type; ?>&id=<?php echo $row->$id; ?>&current=<?php echo $row->$field_name ?>"><?php echo $display; ?></a></td>
                        <?php }
                        for($i=0;$i<sizeof($hierarchy[$counter]); $i++){
                                if($hierarchy[$counter][$i] != 'Sub Slot 0'){
                                    echo '<td class="hierarchy-list"><a href="' . $links[$counter][$i] . '">' . $hierarchy[$counter][$i] . '</a></td>';
                                }else{
                                    echo '<td class="hierarchy-list"></td>';
                                }
                        } ?>
                    <?php if (isset($extension[0])) { //check if any extension data is present for any object
                            $tooltipCreated = false; //flag to indicate when to create tooltip div - set to false for each new search result
                            if($type == 'connection' && isset($extension_values[$counter])){ //if type is connection and external ids present, loop through array of external entity data & external id's
                              $external_entity_counter = 0; //counter for each instance of external entity
                              while($external_entity_counter < sizeof($extension_values[$counter])){ //while more records for another external entity name                                
                                $i = 0; //initialize counter for extension data field title
                                foreach ($extension_values[$counter][$external_entity_counter] as $row): //for each ID (row) within current external entity whithin current connection
                                        if(isset($row) && (string)$row !== '') { //if external ID present and and blank
                                            //check if tooltip div created, if not create it
                                            if($tooltipCreated === false){ ?>
                                                <div class="tooltip <?php echo $id_number ?>">
                                                <?php $tooltipCreated = true;
                                            } 
                                            if($i == 0){ $class = 'entity-name';} //add class for entity title or entity property
                                            else{ $class = 'entity-id';} ?>
                                            <p><span class="label <?php echo $class; ?>"><?php echo $extension[0][$i] . ': '?></span><?php echo $row;?></p>
                                            <?php 
                                            $i +=2; //increment field title counter by 2 because array holds printable field title and database field title for each field
                                        }else{
                                            $i +=2; //increment to next field title even if last one was blank                                          
                                        }
                                endforeach; 
                                $external_entity_counter++; //increment to get name and ID's of next external entity
                                } //end while
                            }//end if connection type
                            else{ //not connection type, treat normally
                                for($j=0;$j<sizeof($extension);$j++){ 
                                    //temporary variable to hold extension field name
                                    $ext = $extension[$j][1];
                                    if(isset($row->$ext) && (string)$row->$ext !== '') {
                                        //check if tooltip div created, if not create it
                                        if($tooltipCreated === false){ ?>
                                            <div class="tooltip <?php echo $row->$id; ?>">
                                            <?php $tooltipCreated = true;
                                        } ?> 
                                        <p><span class="label"><?php echo $extension[$j][0] . ': '?></span><?php echo $row->$ext;?></p>
                                    <?php } 
                                }//end for
                                }//end else
                                if($tooltipCreated){ //close div tag if created?> 
                                    </div>
                              <?php }//end if
                        }//end if extension data present ?>
                    </tr>
               <?php 
               $counter++;//increase search record counter
               endforeach; ?>
              </tbody>
            </table>                          
                <?php } 
                    if(isset($_POST['query']) && !isset($results)){?>
                    <table id="currentlist">
                        <tr class="empty"><td>There are no results for your query.</td></tr>
                    </table>                  
                <?php }?>
     </div>
     <p><a href="<?php echo base_url(); ?>">↩ Home</a></p>
     <?php if($this->session->userdata('is_logged_in')){ //if logged in, display edit tables link ?>
        <p><a class="crud" href="<?php echo base_url() . 'admin/edit'; ?>">Edit All Tables</a></p>
     <?php } ?>     
    </body>
</html>