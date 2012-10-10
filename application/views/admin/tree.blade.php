<ol class="sortable">
		<li id="list_1"><div>Item 1</div>
			<ol>
				<li id="list_2"><div>Sub Item 1.1</div>
				<li id="list_3"><div>Sub Item 1.2</div>
			</ol>
		<li id="list_4" class="no-nest"><div>Item 2 (no-nesting)</div>
		<li id="list_5"><div>Item 3</div>
			<ol>
				<li id="list_6" class="no-nest"><div>Sub Item 3.1 (no-nesting)</div>
				<li id="list_7"><div>Sub Item 3.2</div>
					<ol>
						<li id="list_8"><div>Sub Item 3.2.1</div>
					</ol>
			</ol>
		<li id="list_9"><div>Item 4</div>
		<li id="list_10"><div>Item 5</div>
	</ol>
<a href="#" id="serialize">Naar array</a>
<script>
	$(document).ready(function() {
		
		$('ol.sortable').nestedSortable({
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
			serialized = $('ol.sortable').nestedSortable('serialize');
			console.log(serialized);
		})


	});



</script>