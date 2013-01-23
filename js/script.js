$(function(){
    
    // plugin to allow clickoutside event
    (function($,c,b){$.map("click dblclick mousemove mousedown mouseup mouseover mouseout change select submit keydown keypress keyup".split(" "),function(d){a(d)});a("focusin","focus"+b);a("focusout","blur"+b);$.addOutsideEvent=a;function a(g,e){e=e||g+b;var d=$(),h=g+"."+e+"-special-event";$.event.special[e]={setup:function(){d=d.add(this);if(d.length===1){$(c).bind(h,f)}},teardown:function(){d=d.not(this);if(d.length===0){$(c).unbind(h)}},add:function(i){var j=i.handler;i.handler=function(l,k){l.target=k;j.apply(this,arguments)}}};function f(i){$(d).each(function(){var j=$(this);if(this!==i.target&&!j.has(i.target).length){j.triggerHandler(e,[i.target])}})}}})(jQuery,document,"outside");    
     
    //fade in current breadcrumb
    $('.current').hide();
    $('.current').fadeIn('slow');
    
//    $('#field-circuit').removeClass('chosen-multiple-select').addClass('chosen-select');
//    $('#field-circuit').removeAttr('multiple');
//    //$('#field_circuit_chzn').removeClass('chzn-container-multi').addClass('chzn-container-single');
//    
//    
//    if($('#field_circuit_chzn > ul > li').size() > 2){
//        alert("Can only select 1 circuit for connection");
//    }

    //focus cursor on search box when page loads
    $('#search_field').focus();
    
    //disable right click context menu
    document.oncontextmenu = function() {return false;};
    
    //expand main menu when right click anywhere on page
    $('html').mousedown(function(e) {
        e.stopPropagation();
        if (e.which === 3) {
            /* Right Mousebutton was clicked! */
            $('#main-menu').stop().animate({
		left: '0'//,
		//backgroundColor: 'rgb(255,255,255)'
            },
		200,
		'easeInSine'
            ); // end animate
	 }
    });
    
    //hide menu and tooltips when click anywhere on page
    $('html').click(function() {
        $('#main-menu').stop().animate({
            left: '-265px',
            backgroundColor: '#000'
        },
            800,
            'easeOutBounce'
        ); // end animate
        $('.tooltip').hide();
    });
    
    //prevent hiding menu when click anywhere within menu
    $('#main-menu').click(function(event){
        event.stopPropagation();
    }); 

//    $('#main-menu').hover(
//        function() {
//            $(this).stop().animate({
//		left: '0'//,
//		//backgroundColor: 'rgb(255,255,255)'
//            },
//		200,
//		'easeInSine'
//            ); // end animate
//	 }, 
//	 function() {
//            $(this).stop().animate({
//		left: '-325px',
//		backgroundColor: '#223560'
//            },
//		800,
//		'easeOutBounce'
//            ); // end animate
//	 }
//  ); // end hover
    
    //Ajax version of login
//    $('#login_form').submit(function(){
//        var creds = $('#login_form').serialize();
//        $.post('login/ajax_creds', creds, function processData(data){
//        if(data=='pass'){
//            $('#login_container').html('<h3>Success!</h3>');
//        }else{
//            $('#login_container h3').html('<h3 class="error">Login failed. Try again.</h3>');
//        }
//        });
//        return false;
//    });

    //Ajax call to retrieve shelf data for shelf visual
    $('.shelf-camera').click(function() {
        var shelfId = $(this).next().attr('class');
        console.log(shelfId);
        shelfId = shelfId.substring(19); //get shelf id from class name
        var $shelfDiv = $('.visual' + shelfId);
        var data = 'shelf_id=' + shelfId + '&slot_total=' + $shelfDiv.attr("data-slots");
        console.log(data);
        
        $.ajax({
        url: "../visual/get_visual_shelf",  
        type: "GET",        
        data: data,  
        dataType: 'json',
        cache: false,
        success: function (returnData) { 
            //add the content retrieved from ajax and put it in the #content div
            var slotCount = returnData['slot_count'];
            var number_of_ports = returnData['number_of_ports'];

            $shelfDiv.html(returnData['html']);
            //$shelfDiv.append('<p class="clear">' + returnData['slot_count'] + ' slots of ' + $shelfDiv.attr("data-slots") + ' available </p>');
            var shelf_width = 700; //700 pixels is width of visual shelf. to change, also change css .visual-shelf width
            var row_width = 0; //width of current row so far
            var multiplier = 1; //number of times to multiply the height. each time a new row is needed, add 1
            $shelfDiv.find('.visual-slot').each(function(){
                row_width += $(this).outerWidth( true ); //add current slot width to width of current row
                if(row_width > shelf_width){ //if current width of row is wider than shelf width
                    multiplier++; //increase multiplier
                    row_width = $(this).outerWidth( true ); //add current slot to new row width total
                }
            });
            $shelfDiv.css('height', multiplier*322); //322 is height of one shelf row, multiply it by number of rows needed

            if(number_of_ports > 100) { //if over 100 ports, expand visual-slot width to 700px and give ports lcx-port class
                $shelfDiv.find('.visual-slot').css('width', '700px');
                $shelfDiv.find('span').addClass('lcx-port');
            }             
            $shelfDiv.show(200);       
        }
        
        });
    });
    
    //Ajax call to retrieve bay data for bay visual
    $('.bay-camera').click(function() {
        var bayId = $(this).next().attr('class');
        console.log(bayId);
        bayId = bayId.substring(17); //get bay id from class name
        var $bayDiv = $('.visual' + bayId);
        var bayHeight = $bayDiv.attr("data-bay-height");
        var data = 'bay_id=' + bayId + '&bay_height=' + $bayDiv.attr("data-bay-height");
       
        $.ajax({
        url: "../visual/get_bay_data",  
        type: "GET",        
        data: data, 
        dataType: 'json',
        cache: false,
        success: function (returnData) { 
            console.log(returnData);
            //add the content retrieved from ajax and put it in the #content div
            //var temp = data[0];
            $bayDiv.html(returnData['html']);
            //$bayDiv.before('<p>Bay Height: ' + bayHeight + '</p>');
            if(typeof(bayHeight) != "undefined" && bayHeight !== ''){
                //$bayDiv.css('height', bayHeight * 9);
            }else{
                $bayDiv.append('(bay height missing)');
            }
             
            //display the body with fadeIn transition
            //showVisual(event, this)
            $bayDiv.show(200); 
            $bayDiv.find('div').mouseover(function(evt){
                showShelfTooltip(evt, this);
            }).mouseout(function(){
                $('.visual-shelf-bay-tooltip').remove();
            });
        }
        
        });
    });   
    
    
    //save form default values of create user form to use if field is left blank
    var defaultVals = {};
    function saveDefaults(){
        $("input[type='text']").each(function () {
            defaultVals[$(this).attr('name')] = $(this).attr('value');
        });   
    }
    //call function to save default form values
    saveDefaults();
    
    //show extra data when hover over inventory item
    $('#currentlist .list').mouseover(function(evt){
        $('.tooltip').hide(); //hide any tooltips already showing
        showTooltip(evt, this);
    });
    
    //prevent hiding tooltip when click anywhere within tooltip
    $('.tooltip').click(function(event){
        event.stopPropagation(); //stop click from popping on html element
    });       
    
    //slide out login form when click on login
    $('#login h3').on('mouseenter', function(e) {
        e.stopPropagation(); //prevent click event being fired for body tag
        //div when not logged in
	$('.form_container').slideToggle(300);
        //div when logged in
        $('.form-container-logged-in').slideToggle(300);
    }); // end click

    //remove login form when click out of login box
    $('#login').bind( 'clickoutside',function(){
        $('.form_container').hide(); 
        $('.form-container-logged-in').hide()
    });
    
    //when click on text field in add user form, clear default value and change font to black
    $('#add_user input').focus(function (){
        if($(this).val() === defaultVals[$(this).attr("name")]){
            $(this).val("");   
            $(this).removeClass('grey_font').addClass('black_font');         
        }
    });
    
    //if text field is left blank in add user form, change to grey font and reset to default value
    $('#add_user input').focusout(function (){
        if($(this).val() === ''){
            $(this).removeClass('black_font').addClass('grey_font').val(defaultVals[$(this).attr("name")]);
        }
    });
    
    //display inventory item details in a tooltip
    function showTooltip(event, obj){
        //get class of link object that was hovered
        var currentId = $(obj).attr('id');
        //get div with same class number
        var current = $('div.' + currentId);
        //if there is a div with same id number, set position of tooltip 
        if(current.val() !== null){
            positionTooltip(event, current);
            var options = {};
            $(current).fadeIn(300); //toggle( 'slide', options, 200 )
        }
    }
    
    //get position for where to display tooltip details
    function positionTooltip(event, obj){
        if($('#currentlist').width() <= 600){
            var offset = $('#currentlist').offset();
            var horizontal = offset.left;
            var vertical = offset.top;
            horizontal = horizontal + $('#currentlist').width() + 20;
            var height = obj.height();
            var windowHeight = $(window).height();
            var scrollTop = $(window).scrollTop();
            var scrollBottom = scrollTop + windowHeight;
            if(vertical - 100 < scrollTop){
                vertical = event.pageY -6;
            }
            if(vertical + height > scrollBottom){
                vertical = vertical - height;
            }
            //this commented code makes the hover stay in one spot even after scrolling by updating the spot
//            if(vertical - 120 < scrollTop){
//                vertical = vertical + (scrollTop - 180);
//            }            
            $(obj).css({
                'position': 'absolute', 
                'top':vertical, 
                'left':horizontal
            });  
        }else{
            var tPosx = event.pageX +60;
            var tPosy = event.pageY +1;
            height = obj.height();
            //adjust position so tooltip does not go past bottom of screen
            scrollTop = $(window).scrollTop();
            windowHeight = $(window).height();
            scrollBottom = scrollTop + windowHeight;
            if(tPosy + height > scrollBottom){
                tPosy = tPosy - height;
            }
            $(obj).css({
                'position': 'absolute', 
                'top':tPosy, 
                'left':tPosx
            });
        }
    }
    

    function showShelfTooltip(event, obj){
        //get height of current shelf
        var currentShelfHeight = $(obj).height();
        //create div to display height
        var $current = $('<div class="visual-shelf-bay-tooltip">Height: ' + currentShelfHeight/9 + '</div>');
        $('.visual-bay').prepend($current);
        if($current.val() !== null){
            positionTooltip(event, $current);
            $current.fadeIn(300); //toggle( 'slide', options, 200 )
        }
    }
    
    //show visual of shelf and bay when hover over camera icon
//    $('#currentlist .icon').mouseover(function(){
//        showVisual(event, this);
//    }).mouseout(function(){
//        $('.shelf').hide();
//        $('.bay').hide();
//    });  
//    
    //show visual of shelf and bay when click camera icon
//    $('#currentlist .icon').click(function() {
//        showVisual(event, this);
//    });

    //hide bay & shelf visuals when click anywhere on the page besides the visual itself (or icon to show it)
    $(document).click(function(e) {
        if (!$(e.target).is('#currentlist .icon') && !$(e.target).is('.visual-shelf') && !$(e.target).is('.visual-bay') && !$(e.target).is('.visual-shelf-bay') && !$(e.target).is('.visual-slot') && !$(e.target).is('.visual-port')) {
            $('.visual-shelf').hide(200);
            $('.visual-bay').hide(200);
        }
    });

    //display visual details for bay & shelf
    function showVisual(event, obj){
        $('.visual-shelf').hide(200);
        $('.visual-bay').hide(200);
        //get class of link object that was hovered
        var currentId = $(obj).parent().prev().children().attr('id');
        //get div with same class number
        var current = $('div.visual' + currentId);
        //if there is a div with same id number, set position of tooltip 
        if(current.val() !== null){
            positionVisual(event, current);
            //var number_of_slots = current.attr('data-slots');
            
            //current.css('width', String(100*number_of_slots) + 'px');
            var options = {};
            current.show(200); //toggle( 'slide', options, 200 )
        }
    }
    
    //get position for where to display tooltip details
    function positionVisual(event, obj){
        firstIcon = $('.icon').first();
        var position = firstIcon.position();
        var tPosx = position.left + 60;
        if(tPosx < 260) {tPosx = 260;}
        var tPosy = position.top + 1;
        if(tPosy < 220) {tPosy = 220;}
        var height = obj.height();
        //adjust position so tooltip does not go past bottom of screen
        var wHeight = $(document).height();
        if(tPosy + height > $(document).height()){
            tPosy = tPosy - height;
        }
        $(obj).css({
            'position': 'absolute', 
            'top':tPosy, 
            'left':tPosx
        });
    }    

//give warning before deleting item from database
$('.delete-link').click( function(){
    var delete_url = $(this).attr('href')		

    if( confirm( "Are you sure you want to delete this record?\nTHIS CANNOT BE UNDONE!" ) )
    {
        $.ajax({
            url: delete_url,
            dataType: 'json',
            success: function(data)
            {	
                if(data.success == false){
                    $('#report-error').slideUp('fast');
                    $('#report-error').html(data.error_message + 'Ensure child data has been removed first');
                    $('#report-error').slideDown('normal');
                    //alert(data.error_message);
                }else{
                    var url = delete_url;
                    var id = url.substring(url.lastIndexOf('/') + 1);
                    var type = url.split("/");
                    type = type[5];                                                                                
                     if(type == 'circuit'){ //go back to all circuits view
                        window.location = base_url + 'inv/menu?type=' + type + 's';
                    }else if(type == 'connection'){ //go back to previous sub port of connection
                        window.location = previous_url;
                    }
                    else{ //go back to most recent place in hierarchy
                        window.location = current_url;
                    }
                }
            },
            error: function (request, status, error) {
                alert(request.responseText);
            }            
        });
    }
    return false;
});

    
    $('.tablesorter').tablesorter({widgets: ['zebra']}); 
    //$(".various").fancybox();
    //ADD IN A BUTTON TO ADD TO DROPDOWN
    //$('select#field-connection_id').parent().append('<a href="../connection_popup/add/" class="various fancybox.ajax">Add New Connection</a>');
    


//    function resizeMe(){
//
//        //Standard height, for which the body font size is correct
//        var preferredWidth = 1024;  
//        var fontsize = .85;
//
//        var displayWidth = $(window).width();
//        if(displayWidth-preferredWidth > 100 || preferredWidth-displayWidth > 100){
//            var percentage = displayWidth / preferredWidth;
//            var newFontSize = Math.floor(fontsize * percentage) - 1;
//            $("body").css("font-size", percentage + 'em');
//        }else{
//            $("body").css("font-size", percentage + 'em');
//        }
//    }
//    
//    $(window).bind('resize', function()
//    {
//        resizeMe();
//    }).trigger('resize');

}); //end of document.ready