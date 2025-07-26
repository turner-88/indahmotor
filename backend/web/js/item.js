function calculatePriceGroup(base, group) {
	
	default_price 	= $('#item-purchase_gross_price').val();
	discount 		= parseFloat($('#'+group+'-discount').val());
	price 			= parseFloat($('#'+group+'-price').val());
	
	if (base == "discount") $('#' + group + '-price').val(eval(default_price) - eval(discount/100*default_price));
	if (base == "price") {
		calculated_discount = (default_price - price) / default_price * 100;
		if (eval(calculated_discount) < 0) calculated_discount = 0;
		$('#' + group + '-discount').val(parseFloat(calculated_discount).toFixed(2));
	}
}

function calculateGrossPrice() {

	net_price = parseFloat($('#item-purchase_net_price').val());
	discount = parseFloat($('#item-purchase_discount').val());

	gross_price = net_price/ (100-discount) * 100;
	$('#item-purchase_gross_price').val(Math.round(gross_price));
}

$(document).ready(function() {
	$('.price-groups').on("keyup", function (e) {
		pre_params = $(this).attr('id');
		params = pre_params.split('-');
		console.log(params);
		base = params[1];
		group = params[0];
		if ((e.keyCode >= 48 && e.keyCode <= 57) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 190 || e.keyCode == 8 || e.keyCode == 46 || e.keyCode == 189) calculatePriceGroup(base, group);
	});

	$('.price-groups').on("change", function () {
		pre_params = $(this).attr('id');
		params = pre_params.split('-');
		console.log(params);
		base = params[1];
		group = params[0];
		calculatePriceGroup(base, group);
	});
});