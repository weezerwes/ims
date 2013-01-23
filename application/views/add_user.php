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
            <div id="add_user" class="forms">
                <span class="heading">Create An Account</span>

                <fieldset>
                    <legend>Personal Information</legend>
                    <?php echo validation_errors('<p class="error">'); ?>
                    <?php
                    echo form_open('admin/create_user');
                    $params = array('name' => 'first_name', 'class' => 'grey_font');
                    echo form_input($params, set_value('first_name', 'First Name'));
                    $params = array('name' => 'last_name', 'class' => 'grey_font');
                    echo form_input($params, set_value('last_name', 'Last Name'));
                    $params = array('name' => 'email_address', 'class' => 'grey_font');
                    echo form_input($params, set_value('email_address', 'Email Address'));
                    //echo form_input('first_name', set_value('first_name', 'First Name'));
                    //echo form_input('last_name', set_value('last_name', 'Last Name'));
                    //echo form_input('email_address', set_value('email_address', 'Email Address'));
                    ?>
                </fieldset>

                <fieldset>
                    <legend>Login Information</legend>
                    <?php
                    $params = array('name' => 'username', 'class' => 'grey_font');
                    echo form_input($params, set_value('username', 'Username'));
                    $params = array('name' => 'password', 'class' => 'grey_font');
                    echo form_input($params, set_value('password', 'Password'));
                    $params = array('name' => 'password2', 'class' => 'grey_font');
                    echo form_input($params, set_value('password2', 'Password Confirm'));                    
                    //echo form_input('username', set_value('username', 'Username'));
                    //echo form_input('password', set_value('password', 'Password'));
                    //echo form_input('password2', set_value('password2', 'Password Confirm'));

                    echo form_submit('submit', 'Create Account');
                    echo form_close();
                    ?>

                    
                </fieldset>
            </div>
       </div>
    </body>
</html>