            <div id="main-menu">
                <ul>
                    <li><a class="main-menu-links" href="<?php echo base_url() . 'inv/menu?type=circuits'; ?>">View Circuits</a><hr class="hr-main-menu"></li>
                    <?php if($this->session->userdata('is_logged_in')){ ?>
                        <li><a class="main-menu-links" href="<?php echo base_url() . 'admin/circuit/add'; ?>">Add Circuit</a><hr class="hr-main-menu"></li>
                        <li><a class="main-menu-links" href="<?php echo base_url() . 'admin/connection/add'; ?>">Add Connection</a><hr class="hr-main-menu"></li>
                    <?php } ?>                        
                    <li><a class="main-menu-links" href="#">Reports</a><hr class="hr-main-menu"></li>
                    <li><a class="main-menu-links" href="<?php echo base_url() . 'search'; ?>">Search</a><hr class="hr-main-menu"></li>
                </ul>
            </div> 