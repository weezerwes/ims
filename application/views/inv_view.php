<?php ?>

<!DOCTYPE html>

<html>
    <head>
        <title><?php echo ($this->session->userdata('is_logged_in') ? "Inventory Management - Peerless Network" : "Inventory View - Peerless Network") ?></title>
        <meta http-equiv="Content-Type" content ="text/html; charset=UTF-8">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/inventory.css" type="text/css">
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
                
                    echo '<h2 class="heading">' . ucwords(preg_replace('/_/', ' ', $_SESSION['type'][sizeof($_SESSION['type']) - 1])) . '</h2>';
                    if($this->session->userdata('is_logged_in')){ 
                        echo '&nbsp; <a class="crud" href="' . base_url() . 'admin/' . $type . '/add">add</a>';    
                    }?>         
            <hr class="list">
                <table id="currentlist">
                    <?php if(isset($$type) && sizeof($$type)!==0){
                            foreach ($$type as $row):
                                $display = $row->$field_name;
                                $id_number = $row->$id; ?>
                    <tr>
                        <?php 
                            if($type == 'connection'){ //add port_id to link if type is connection to track which port connection is associated with because connections are associated with multiple ports ?>
                                <td><a class="list" id="<?php echo $row->$id; ?>" href="<?php echo base_url(); ?>inv/menu?type=<?php echo $type; ?>&id=<?php echo $row->$id; ?>&current=<?php echo $row->$field_name ?>&port=<?php echo $row->port_id ?>"><?php echo $display; ?></a></td>
                        <?php }else{ //list data normally ?>
                            <td><a class="list" id="<?php echo $row->$id; ?>" href="<?php echo base_url(); ?>inv/menu?type=<?php echo $type; ?>&id=<?php echo $row->$id; ?>&current=<?php echo $row->$field_name ?>"><?php echo $display; ?></a></td>
                        <?php } 
                        if($type == 'shelf'){ ?>
                            <td><div class="icon icon-camera <?php echo $type . '-camera'; ?>"></div><div class="<?php echo 'visual-' . $type . ' visual' . $id_number; ?>" data-slots="<?php echo $row->number_of_slots; ?>">Hello</div></td>
                        <?php }else if($type == 'bay'){ ?>                            
                            <td><div class="icon icon-camera <?php echo $type . '-camera'; ?>"></div><div class="<?php echo 'visual-' . $type . ' visual' . $id_number; ?>" data-bay-height="<?php echo $row->bay_height; ?>">Hello</div></td>                         
                        <?php } if($this->session->userdata('is_logged_in')){ //print crud links if logged in ?>
                            <td><a class="crud" href="<?php echo base_url(); ?>admin/<?php echo $type; ?>">view</a></td>
                            <td><a class="crud" href="<?php echo base_url(); ?>admin/<?php echo $type; ?>/edit/<?php echo $row->$id; ?>">edit</a></td>
                            <td><a class="crud delete-link" href="<?php echo base_url(); ?>admin/<?php echo $type; ?>/delete/<?php echo $row->$id; ?>">delete</a></td>
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
                            else{
                                for($j=0;$j<sizeof($extension);$j++){ 
                                    //temporary variable to hold extension field name
                                    $ext = $extension[$j][1];                                        
                                    if(isset($row->$ext) && (string)$row->$ext !== '') {
                                        //check if tooltip div created, if not create it
                                        if($tooltipCreated === false){ ?>
                                            <div class="tooltip <?php echo $id_number ?>">
                                            <?php $tooltipCreated = true;
                                        } ?> 
                                    <p><span class="label"><?php echo $extension[$j][0] . ': '?></span><?php echo $row->$ext;?></p> 
                                    <?php }
                                }//end for
                            }// end if
                            if($tooltipCreated){ //close div tag if created?> 
                                </div>
                            <?php }//end if
                        }//end if extension data present ?>
                    </tr>

                        <?php endforeach;
                    }else{?>
                        <tr class="empty">
                            <td>None
                                <?php if($this->session->userdata('is_logged_in')){ //print crud links if logged in ?>
                                    &nbsp;<a class="crud" href="<?php echo base_url() ?>admin/<?php echo $type; ?>/add">add</a>
                                <?php } ?>
                        </td></tr>
                    <?php }//end if ?>
                </table>
            </div>
                    
           <?php if($type == 'country'){ ?>
            <div class="search_container">
                <h2 class="heading">Search</h2>
                <hr class="list">
                <?php echo form_open('search/index', array('class' => 'search_form forms search_form_home'));
                $types = array('aisle' => 'Aisle', 'bay' => 'Bay', 'building' => 'Building', 'circuit' => 'Circuit', 'connection' => 'Connection', 'city' => 'City', 'location' => 'Location', 'port' => 'Port', 'shelf' => 'Shelf', 'slot' => 'Slot', 'sub_port' => 'Sub Port', 'sub_slot' => 'Sub Slot'); 
                echo form_dropdown('type', $types);
                echo form_input(array('name' => 'query', 'class' => 'form-input-box', 'id' => 'search_field'));
                echo form_submit('search', 'Search'); echo '<br>';
                echo form_checkbox('hierarchy_flag', 'true', FALSE, 'id="hierarchy_flag" class="search-checkbox"') . '<label for="hierarchy_flag" class="search-checkbox-text">Search Within Hierarchy</label>';
                echo form_close(); ?>
            </div>                      
           <?php }else{ ?>
                <p><a href="<?php echo base_url(); ?>">↩ Start Over</a> | <a href="<?php echo base_url() . 'search'; ?>">Search</a></p>
           <?php }
            if($this->session->userdata('is_logged_in')){ //if logged in, display edit tables link ?>
                <p><a class="crud" href="<?php echo base_url() . 'admin/edit'; ?>">Edit All Tables</a></p>
           <?php }?>
        </div> 
    </body>
</html>
