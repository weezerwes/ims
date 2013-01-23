        <header>
            <div id="logo"><a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(); ?>img/peerless-network-logo.png" alt="Peerless Network"></a></div>
            <div id="login" class="forms">
                <div id="login_container">
                    <h3>Login</h3>
                    <div class="form_container">
                        <?php
                        echo form_open('login/validate_credentials', 'id="login_form"');
                        echo form_label('Username', 'username');
                        echo form_input('username');
                        echo form_label('Password', 'password');
                        echo form_password('password');
                        echo form_submit('submit', 'Login');
                        echo form_close();
                        ?>
                    </div>
                </div>
            </div>

                <h1 id="title">Peerless Network Inventory</h1>
           
            <div id="toprow"></div>
        </header>
