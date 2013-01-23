            <ul id="crumbs">
                 <?php 
                 if(sizeof($_SESSION['list'] > 0)){ 
                     for ($i = 0; $i < sizeof($_SESSION['list']); $i++) : 
                        if($i==sizeof($_SESSION['list'])-2 && isset($_SESSION['list'][$i+1]) && ($_SESSION['list'][$i+1] == 'Sub Slot 0')){
                            echo '<li class="current"><a href="' . $_SESSION["previouslevelink"][$i] . '">' . $_SESSION['list'][$i] . '</a></li>';
                        }else if($_SESSION['list'][$i] != 'Sub Slot 0'){
                            if ($i != sizeof($_SESSION['list']) - 1) {
                                echo '<li class="breadcrumb"><a href="' . $_SESSION["previouslevelink"][$i] . '">' . $_SESSION['list'][$i] . '</a></li>';
                            } else { //breadcrumb of current level - assign different class
                                echo '<li class="current"><a href="' . $_SESSION["previouslevelink"][$i] . '">' . $_SESSION['list'][$i] . '</a></li>';
                            }
                        }
                    endfor;
                 } ?> 
            </ul>