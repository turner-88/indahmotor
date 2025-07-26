$(document).ready(function() {

$('#outgoing-customer_id').on("change", function(e) { 
	$.post("?r=outgoing-sale/get-customer", { customer_id: this.value }, function (response) {
		$('#outgoing-salesman_id').val(response.salesman_id).change();
		outgoingsUnpaid = response.outgoingsUnpaid;
		str = '';
		for (const key in outgoingsUnpaid) {
			str += 'Faktur ID ' + outgoingsUnpaid[key].outgoing_id + ': lewat jatuh tempo ' + outgoingsUnpaid[key].count_of_days_late + " hari.\n";
		}
		if (str != '') {
			if (!confirm(response.name + " memiliki " + Object.keys(outgoingsUnpaid).length + " faktur belum lunas yang telah lewat jatuh tempo.\n\n" + str + "\nLanjutkan transaksi?")) {
				$('#outgoing-customer_id').val('').change();
				$('#outgoing-salesman_id').val('').change();
			}
		}
	}, "json"); 
});

$('#outgoing-customer_id').on("select2:unselecting", function(e) { 
	$('#outgoing-salesman_id').val(0).change();
});

});
