<!DOCTYPE html>

<html>
    <head>
        <title><?php echo "Welcome to $_SERVER[HTTP_HOST]" ?></title>
        <meta http-equiv="Content-Type" content ="text/html; charset=UTF-8">
    </head>
    
    <body>
        <div id="container">
            <p>My view has been loaded.</p>
            
           <pre>
                <?php print_r($country); ?>
            </pre>
            
            <?php foreach($country as $row) : ?>
                <h3><?php echo $row->country_name; ?></h3>
            <?php endforeach; ?>

                <?php foreach ($city as $row) : ?>
                    <h4><?php echo "-$row->city_name"; ?></h4>
                <?php endforeach; ?>

                <?php foreach ($building as $row) : ?>
                    <h5><?php echo "--$row->building_name"; ?></h5>
                <?php endforeach; ?>
                    
                    <p><?php print_r($_SERVER); ?></p>
            
                    
                <?php 
                
                foreach($rows as $r){
                    echo '<h3>' . $r->title . '</h1>';
                }
                ?>
                    
            <p><?php echo $myValue; ?></p>
            <p><?php echo $anotherValue; ?></p>
            
        </div>
    </body>
</html>
