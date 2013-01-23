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
            <!--import logo & breadcrumbs-->
            <?php include("_header_admin.php"); ?> 
            <!--import breadcrumbs-->
            <?php include("_no_breadcrumbs.php"); ?>
            <?php include("_main_menu.php"); ?>            
            <h3>Account successfully created!</h3>
            <h4>Username: <?php echo $username; ?> </h4>
            <?php echo anchor(base_url(), 'Return Home'); ?>
        </div>
    </body>
</html>
