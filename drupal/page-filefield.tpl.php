<?php 
// there is probably a better way to js escape this but we DON'T want to pass raw input
// to the script below
$field = htmlspecialchars($_GET['field']); ?>

<html>
<head>
	<title>Browse Images</title>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/jquery-ui.min.js"></script>
	<link type="text/css" rel="stylesheet" href="http://readysetlaunch.com/css/image_library.css" media="all" />
	<script type="text/javascript">
	
		function refreshImages(url)
		{
			$('.image-library-images').animate({opacity:0}, 500, function() {
				$('.image-library-images').load(url, function() {
					$('.image-library-images').animate({opacity:1}, 1000, function() {});
				});
				
			})
			
		    setHeight();
		}
	
		function setHeight()
		{
		    var nHt = $(window).height() - $('.image-library-header').outerHeight(true);
		    $('.image-library-images').css('height', nHt+'px');
		}
		
		$(function() {
		
		    setHeight();
		
		    $(window).resize(function() {
		    	setHeight();
		    });
		
		    $('img').live('click', function() {
		    	var myImg = $(this).attr('alt');
		    	window.opener.$(window.opener.document).find('#<?php echo $field; ?>-your-image').val(myImg);
		    	window.opener.$(window.opener.document).find('#<?php echo $field; ?>-select').trigger('mousedown');
		    	window.close();
		    });
		    
		    <?php if( ! preg_match('/zwyi/', $field)): ?>
		    $('#industry-dropdown').live('change', function() {
		    	var ind_id = $(this).val();
		    	refreshImages('http://readysetlaunch.com/api/pages/image_library/industry/'+ind_id+'/1');
		    });
		    

			$( "#tag-search" ).autocomplete({
				minLength: 0,
				source: availableTags,
				focus: function( event, ui ) {
					$( "#tag-search" ).val( ui.item.name );
					return false;
				},
				select: function( event, ui ) {
					$('#tag-search').val( ui.item.name );
					$('#tag-search-id').val( ui.item.id );
					return false;
				}
			})
			.data( "autocomplete" )._renderItem = function( ul, item ) {
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append( "<a>" + item.name + " ("+item.ct+")</a>" )
					.appendTo( ul );
			};
			
			$('#tag-search-submit').live('click', function(e) {
				e.preventDefault();
				refreshImages('http://readysetlaunch.com/api/pages/image_library/tag/'+$('#tag-search-id').val()+'/1');
			});
			<?php endif ?>
		
		});
	</script>
</head>
<body>
	<?php print $content; ?>
</body>
</html>
