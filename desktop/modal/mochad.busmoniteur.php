<?php
if (!isConnect('admin')) {
    throw new Exception('401 Unauthorized');
}
include_file('3rdparty', 'jquery.tablesorter/theme.bootstrap', 'css');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.min', 'js');
include_file('3rdparty', 'jquery.tablesorter/jquery.tablesorter.widgets.min', 'js');
?>

<table id="table_BusMonitor" class="table table-bordered table-condensed tablesorter">
    <thead>
        <tr>
            <th>{{Date}}</th>
            <th>{{RX/TX}}</th>
            <th>{{RF/Pl}}</th>
            <th>{{Message}}</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
<script>
initTableSorter();
getBusMonitor();
function getBusMonitor () {
	$.ajax({
		type: 'POST',
	async: false,
	url: 'plugins/mochad/core/ajax/mochad.ajax.php',
		data: {
			action: 'getCacheMonitor',
		},
		dataType: 'json',
		global: false,
		error: function(request, status, error) {
			setTimeout(function() {
				getBusMonitor()
			}, 100);
		},
		success: function(data) {
			if (data.state != 'ok') {
				$('#div_alert').showAlert({message: data.result, level: 'danger'});
				return;
			}
			$('#table_BusMonitor tbody').html('');
			//alert(data.result);
			var monitors=jQuery.parseJSON(data.result);
			jQuery.each(monitors.reverse(),function(key, value) {
			  $('#table_BusMonitor tbody').append($("<tr>")
					.append($("<td>").text(value.datetime))
					.append($("<td>").text(value.monitor.TxRx))
					.append($("<td>").text(value.monitor.RfPl))
					.append($("<td>").text(value.monitor.Message)));
			});				
			$('#table_BusMonitor').trigger('update');
				setTimeout(function() {
					getBusMonitor()
				}, 100);
			
		}
	});
}		   
</script>
			