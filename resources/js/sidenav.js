$(function() {

    // sidenav Toggler
    function sidenavToggle(toogle) {
        var sidenav = $('#sidenav');
        var content = $('.content');
        if( toogle ) {
            $('.notyf').removeAttr( 'style' );
            sidenav.css({'display': 'block', 'x': -300});
            sidenav.transition({opacity: 1, x: 0}, 250, 'in-out', function(){
                sidenav.css('display', 'block');
            });
            if( $( window ).width() > 960 ) {
                content.transition({marginLeft: sidenav.css('width')}, 250, 'in-out');
            }
        } else {
            $('.notyf').css({width: '90%', margin: '0 auto', display:'block', right: 0, left: 0});
            sidenav.css({'display': 'block', 'x': '0px'});
            sidenav.transition({x: -300, opacity: 0}, 250, 'in-out', function(){
                sidenav.css('display', 'none');
            });
            content.transition({marginLeft: 0}, 250, 'in-out');
        }
    }

    $('#sidenav_toggle').click(function() {
        var sidenav = $('#sidenav');
        var content = $('.content');
        if( sidenav.css('x') == '-300px' || sidenav.css('display') == 'none' ) {
            sidenavToggle(true)
        } else {
            sidenavToggle(false)
        }
    });

    function resize()
    {
        var sidenav = $('#sidenav');
        var content = $('.content');
		content.removeAttr( 'style' );
		if( $( window ).width() < 960 && sidenav.css('display') == 'block' ) {
			sidenavToggle(false);
		} else if( $( window ).width() > 960 && sidenav.css('display') == 'none' ) {
			sidenavToggle(true);
		}
    }

    if($( window ).width() < 960) {
        sidenavToggle(false);
    }

	$( window ).resize(function() {
		resize()
	});

    $('.content').click(function() {
        if( $( window ).width() < 960 ) {
            sidenavToggle(false);
        }
    });

});
