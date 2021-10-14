<script type="text/javascript">
function trigger_progress_{id}() {
	$("#progressbar_{id}").progressbar({});
	$.ajax({
		url: "{url}",
		dataType: "text",
		error: function(response) {
			if($("#progressbar_{id}").html == '') {
				setTimeout("trigger_progress_{id}()", 1000);
			}
		},
		success: function(response) {
			var no = parseInt(response);
			if (no < 0) { no = 0; }
			$("#progressbar_{id}").progressbar("option", "value", no);
			if (no < 100) {
				$("#watcher_{id}").html("&nbsp;&nbsp;<small>" + response + "% - {lang_in_progress}</small>");
				setTimeout("trigger_progress_{id}()", 1000);
			} else {
				if(no == 100) {
					$("#watcher_{id}").html("&nbsp;<small>100 % - {lang_finished}</small>");
				} else {
					setTimeout("trigger_progress_{id}()", 1000);
				}
			}	
		}
	});
}
trigger_progress_{id}();
</script>
<div class="progress_bar">
	<div id="progressbar_{id}"></div>
</div>
<div id="watcher_{id}" class="progress_watcher">{lang_in_progress}</div>

