$(function(){

	$(".date").datepicker({ dateFormat: "yy-mm-dd" });
	$(".time").timepicker({});
	$(".datetime").datetimepicker({ dateFormat: "yy-mm-dd" });

	window.resetForm= function($form) {
	$form.find('input:text, input:password, input:file, select, textarea').val('').attr("value","");
	$form.find('input:radio, input:checkbox')
		 .removeAttr('checked').removeAttr('selected');
	}

	$("input[type='reset']").click(function(){
		resetForm( $(this).closest("form") );
	});
})