<?php ?>

<!DOCTYPE html>

<html>
    <head>
        <title><?php echo "Inventory Management - Peerless Network" ?></title>
        <meta http-equiv="Content-Type" content ="text/html; charset=UTF-8">
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/inventory.css" type="text/css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
        <script src="<?php echo base_url(); ?>js/jquery-ui-1.8.24.custom.min.js"></script>   
        <script src="<?php echo base_url(); ?>js/jquery.tablesorter.min.js"></script>                
        <script src="<?php echo base_url(); ?>js/script.js"></script>
    </head>

    <body>
      <div id="maincontent">
        <!--import logo-->
        <?php include("_header_admin.php"); ?> 
        <!-- import no breadcrumbs       -->
        <?php include("_no_breadcrumbs.php"); ?>      
        <?php include("_main_menu.php"); ?>
            <div id="menu">      
                <h2 class="heading">Tables</h2>
                <hr class="list">                
                <table id="currentlist">
                    <?php
                   if(sizeof($tables)!==0){
                        
                    foreach ($tables as $row):

                        $display = $row;
                    if($row != 'user'){ ?>
                        <tr>
                            <td><a class="list" href="<?php echo base_url(); ?>admin/<?php echo $display; ?>"><?php echo $display; ?></a></td>        
                        </tr>
                    <?php }
                    endforeach;
                    }else{?>
                        <tr class="empty"><td>None</td></tr>
                    <?php }//end if
                    ?>
                </table>
            </div>

            <p><a href="<?php echo base_url(); ?>">↩ Start Over</a></p>
        </div>
    </body>
</html>
