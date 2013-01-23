        <header>
            <div id="logo"><a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>img/peerless-network-logo.png" alt="Peerless Network"></a></div>
            <div id="login" class="forms">
                <div id="login_container">
                <h3>Signed in as: <?php echo $this->session->userdata('username');?></h3>
                <div class="form-container-logged-in">
                <?php 
                echo anchor('admin/add_user', 'Create Account');
                echo anchor('login/logout', 'Logout');
                ?>
                </div>
                </div>
            </div>
            <h1 id="title">Inventory Management</h1>
            <div id="toprow"></div>
        </header>