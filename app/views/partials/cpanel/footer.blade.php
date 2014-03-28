<div style="margin-bottom: 40px"></div>
<footer class="navbar navbar-fixed-bottom navbar-default footer">
	<div class="container">
		<p class="navbar-text">Copyright &copy; tofazakie</p>
	</div>
</footer>
<script src="{{ asset('packages/noty/packaged/jquery.noty.packaged.min.js') }}"></script>
<script>
	<?php 
	// find latest case
	$latest_case = DB::table('em_case')
		->orderBy('case_id', 'desc')
		->first();

	if ($latest_case) {
		$ld = $latest_case->case_id;
	} else {
		$ld = 0;
	}
	?>

	var latestCaseId = {{ $ld }};

	jQuery(function($) {

		var xhr,
			notif;

		// real time proccess showtime!!
		function ticker() {
			if (xhr && xhr.readyState != 4) {
				xhr.abort();
			}

			xhr = $.ajax({
				url : "{{ action('EmergencyController@postAjaxLatestEmergency') }}",
				type : 'POST',
				dataType : 'json',
				data : {
					case_id: latestCaseId
				},
				success : function(data) {
					latestCaseId = data.case_id;

					notif = noty({
						text : 'User <b>' + data.user_reporter.no_id + ' - ' + data.user_reporter.nama + '</b> telah melaporkan kasus emergency <b>' + data.em_type.type_name + '</b>',
						type : 'warning',
						callback : {
							onClose : function() {
								window.location = "{{ action('HomeController@getIndex') }}?lat=" + data.lat + '&lng=' + data.lon + '&cid=' + data.case_id;
							}
						}
					});
				}
			});
		}

		function updateTicker() {
			ticker();
			setTimeout(updateTicker, 25000);
		}

		updateTicker();

	});
</script>