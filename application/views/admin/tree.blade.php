
<?=$list?>
	
<a href="#" id="serialize">Naar array</a>
<script>
	$(document).ready(function() {
	
		    jQuery.each($('ol li'), function(i, val) {
		    	$(this).attr('id', 'list_'+(i+1));
		    });				   

		$('ol').first().nestedSortable({
			disableNesting: 'no-nest',
			forcePlaceholderSize: true,
			handle: 'div',
			helper:	'clone',
			items: 'li',
			maxLevels: 3,
			opacity: .6,
			placeholder: 'placeholder',
			revert: 250,
			tabSize: 25,
			tolerance: 'pointer',
			toleranceElement: '> div'
		});


		$('#serialize').click(function(){
			serialized = $('ol').first().nestedSortable('serialize');
			console.log(serialized);
			console.log('array');
		})


	});



</script>