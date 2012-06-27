<!DOCTYPE html>

<html>
    <head>
        <title><?php echo "Welcome to $_SERVER[HTTP_HOST]" ?></title>
        <meta http-equiv="Content-Type" content ="text/html; charset=UTF-8">
    </head>
    
    <body>
        <div id="container">
            <p>My view has been loaded.</p>
            
                <?php 
                
                if (count($rows) > 0) : ?>
                <?php foreach($rows as $r) : ?>
            
                <h3><?php echo $r->title . " by " . $r->author; ?></h3>
                <div><?php echo $r->contents; ?></div>
            
                <?php endforeach; endif;
                ?>
            
        </div>
    </body>
</html>
