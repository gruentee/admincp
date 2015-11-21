$(this).ready( function() {
    var $image_containers = $("#pics li");
    // Dummy
    $('.button_delete').click(delete_dummy);
	
	// Dummy add function
	var addDummy = function() {
		showMessage("Sorry, das Hinzufügen von Bildern ist in der Demo momentan nicht möglich.");
	}
	$('#add_button').click(addDummy);
    
    $image_containers.each( function(){
        var $this = $(this); // <li>
        
        // Buttons zum verschieben der Bilder
        var $buttons = $this.find('.pic_nav li').slice(0,2);
        // TODO: Button des ersten/letzten Bildes ausgrauen
        $buttons.bind('click', function(e)
        {
            var $this = $(this);
            var $curr_pic_li = $image_containers.has($this);
            var $next_pic_li = $curr_pic_li.next('li');
            var $prev_pic_li = $curr_pic_li.prev('li');
            var current_pic_id = $curr_pic_li.children("img")
                    .attr("id");

            // Class of button that was clicked determines direction
            var dir = $this.find('a').hasClass('button_movel') ? "up" : "down";
            var other_pic_id = (dir == 'up') ?
                $prev_pic_li.children("img").attr("id") :
                $next_pic_li.children("img").attr("id");
           // Erstes oder letztes Bild?
            if( ( $curr_pic_li.is( ":first-child" ) && dir == 'up'  ) ||
                    ( $prev_pic_li.is( ":last-child" ) && dir == 'down') ) {
                if($('.warn_box').length == 0) {
                    var $err_box = $('<div>').addClass('msg_slide warn_box');
                } else {
                    $err_box = $('.warn_box');
                }
                showMessage("Bild kann nicht weiter verschoben werden!");
                //$err_box.appendTo('#msg_wrapper').hide();
                //$err_box.slideDown();
                return false;
            }
            else
            {
                $.getJSON("./changePicOrder.php", {
                    other : other_pic_id,
                    curr : current_pic_id,
                    action : "changePicOrder"
                    },
                    function(response)
                    {
                        if(response.success == 1) { // Kein Fehler
                            // TODO: fade-in / fade-out
                            $curr_pic_li.fadeOut();

                            if(dir == "up") // Anzeige aktualisieren
                            {
                                $prev_pic_li.fadeOut();
                                $curr_pic_li.insertBefore($prev_pic_li);
                                $curr_pic_li.fadeIn('slow');
                                $prev_pic_li.fadeIn('slow');
                                $other_pic = $prev_pic_li.children('img');
                            }
                            else
                            {
                                $next_pic_li.fadeOut();
                                $curr_pic_li.insertAfter($next_pic_li);
                                $curr_pic_li.fadeIn('slow');
                                $next_pic_li.fadeIn('slow');
                                $other_pic = $prev_pic_li.children('img');

                            }
                            // IDs aktualisieren
                             $curr_pic_li.children('img').attr('id', other_pic_id);
                             $other_pic.attr('id', current_pic_id);
                        } // Fehler auf Server-Seite
                        else
                        {
                            if($('.err_box').length == 0) {
                                var $err_box = $('<div>').addClass('msg_slide err_box');
                            } else {
                                $err_box = $('.err_box');
                            }
                            $err_box.text(response.error_msg);
                            $err_box.appendTo('#msg_wrapper').hide();
                            $err_box.slideDown();
                        }
                    }
                );
            }

        });
        // Button zum Setzen des special_flag
        var $sflag_button = $this.children().find('.button_star');
        current_pic_id  = $this.children('img').attr('id');
        $sflag_button.click(current_pic_id, set_pic_sflag);

    });
    
    function set_pic_sflag(event) {
        event.preventDefault();
        pic_id = event.data;
        var $this = $(this);
        $.get( "./sflag.php", { action: 'set', id: pic_id, sflag: 1 }, function(resp ) {
            if( !isNaN( resp ) && resp > 0 ) {
                // remove class from former 'special' pic's star button
                $('a.button_star.enabled').removeClass('enabled');
                console.log($this);
                $this.addClass('enabled');
            }
            else
            {
                window.alert( resp );
            }
        });
    }

    function delete_dummy(event) {
        event.preventDefault();
		showMessage("Die Löschen-Funktion ist in der Demo deaktiviert.");
        return false;
    }
	
	function showMessage(msg) {
        if($('.warn_box').length == 0) {
            var $err_box = $('<div>').addClass('msg_slide warn_box');
        } else {
            $err_box = $('.warn_box');
        }
        $('html,body').animate({
            scrollTop: $('#msg_wrapper').offset().top
        },'slow');
        $err_box.text(msg);
        $err_box.appendTo('#msg_wrapper').hide();
        $err_box.slideDown();	
	}
});
