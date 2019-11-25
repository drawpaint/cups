
$(document).ready(function() {
	$(function() {
		$("#dialog").dialog({
			autoOpen: false
		});
		$("#button").on("click", function() {
			$("#dialog").dialog("open");
		});
	});
	
	// Validating Form Fields.....
	$("#submit").click(function(e) {
		var email = $("#email").val();
		var name = $("#name").val();
	});
});