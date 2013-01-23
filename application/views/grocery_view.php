<!DOCTYPE html>
<html>
    <head>
        <title><?php echo "Inventory Management - Peerless Network" ?></title>
        <meta http-equiv="Content-Type" content ="text/html; charset=UTF-8">
        <?php foreach ($css_files as $file): ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
        <?php endforeach; ?>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/grocery_crud/css/jquery_plugins/chosen/chosen.css" />            
        <link rel="stylesheet" href="<?php echo base_url(); ?>css/inventory.css" type="text/css" />
        <?php foreach ($js_files as $file): ?>
            <script src="<?php echo $file; ?>"></script>
        <?php endforeach; ?>
        <script>var current_url = '<?php if (sizeof($_SESSION["previouslevelink"])>0) { echo $_SESSION["previouslevelink"][sizeof($_SESSION["previouslevelink"])-1];} else {echo base_url();} ?>';
        var base_url = '<?php echo base_url(); ?>';</script>
        <script src="<?php echo base_url(); ?>js/jquery-ui-1.8.24.custom.min.js"></script>   
        <script src="<?php echo base_url(); ?>js/jquery.tablesorter.min.js"></script>                
        <script src="<?php echo base_url(); ?>js/script.js"></script>
        <link rel="stylesheet" href="<?php echo base_url(); ?>assets/fancybox/jquery.fancybox.css" type="text/css" />
<!--        <script type="text/javascript" src="<?php echo base_url(); ?>assets/fancybox/jquery.fancybox.pack.js"></script>      -->
    </head>

    <body>
      <div id="maincontent">        
        <!--import logo & breadcrumbs-->
        <?php include("_header_admin.php"); ?> 
        <!--import breadcrumbs-->
        <?php include("_breadcrumbs.php"); ?>  
        <?php include("_main_menu.php"); ?>
            <div id="menu">                             
                <div>
                    <?php echo $output; ?>

                </div>
                <!-- Beginning footer -->
                <p><a href="<?php echo base_url(); ?>">↩ Start Over</a></p>
                <p><a class="crud" href="<?php echo base_url() . 'admin/edit'; ?>">Edit All Tables</a></p>
            </div>
        </div>
        <!-- End of Footer -->
    </body>
</html>