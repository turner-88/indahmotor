/* var ajaxCallbacks = {
    "simpleDone" : function(response) {
        // This is called by the link attribute "data-on-done" => "simpleDone"
        console.dir(response);
        $("#ajax_result_01").html(response.body);
    },
    "deleteItemDone" : function(response) {
        console.dir(response);
        $("tr[data-key='"+response.id+"'").fadeOut();
    }
} */

var collapseIn = 0;

function calculatePriceGroup(base, group) {
	default_price = $('#incomingitem-gross_price').val();
	discount = parseFloat($('#' + group + '-discount').val());
	price = parseFloat($('#' + group + '-price').val());

	if (base == "discount") $('#' + group + '-price').val(Math.round(eval(default_price) - eval(discount / 100 * default_price)));
	if (base == "price") {
		calculated_discount = (default_price - price) / default_price * 100;
		if (eval(calculated_discount) < 0) calculated_discount = 0;
		$('#' + group + '-discount').val(parseFloat(calculated_discount).toFixed(2));
	}
}

function calculateGrossPrice() {
	net_price = parseFloat($('#incomingitem-price').val());
	discount = parseFloat($('#incomingitem-discount').val()).toFixed(2);

	gross_price = net_price/ (100-discount) * 100;
	$('#incomingitem-gross_price').val(Math.round(gross_price));
}

function retrieveItem(shortcode) {
	$('.modal').modal('hide');
	$('#info_new_item').hide();
	$.post("?r=incoming-purchase/get-item-by-shortcode", { item_shortcode: shortcode }, function (response) {
		if (response.isFound) {
			// $('#unitofmeasurement-label').html('(' + response.unit_of_measurement + ')');

			$('#item_shortcode').val(response.shortcode);
			$('#item_name').val(response.name);
			$('#item_brand').val(response.brand);
			$('#item_type').val(response.type);
			$('#item_unit_of_measurement').val(response.unit_of_measurement);
			$('#item_location').val(response.location);

			$('#incomingitem-item_id').val(response.id);
			$('#incomingitem-price').val(Math.round(response.purchase_net_price));
			$('#incomingitem-gross_price').val(Math.round(response.purchase_gross_price));
			$('#incomingitem-discount').val(parseFloat(response.purchase_discount).toFixed(2));

			$('.price-groups').val('');

			response.price_groups.forEach(price_group => {
				$('#' + price_group.priceGroup.name + '-discount').val(parseFloat(price_group.discount).toFixed(2));
				$('#' + price_group.priceGroup.name + '-price').val(Math.round(price_group.price));
			});

			$('#incomingitem-quantity').focus();
		} else {
			$('#info_new_item').fadeIn().fadeOut().fadeIn().fadeOut().fadeIn();
		}
	}, "json");
}

function setFocus() {
	if ($('#item_shortcode').val() == '' && !$('#myModal').hasClass('in') && !$('#myModal2').hasClass('in') && collapseIn == 0) {
		setTimeout(function(){
			$('#item_shortcode').focus();
		}, 100);
	}
}

$(document).ready(function() {
	$('#item_shortcode').focus();
	
	$('#btn-grid').on("focus", function (e) {
		setFocus();
	});
	$('#item_name').on("focus", function (e) { setFocus(); });
	$('#item_brand').on("focus", function (e) { setFocus(); });
	$('#item_type').on("focus", function (e) { setFocus(); });
	$('#item_unit_of_measurement').on("focus", function (e) { setFocus(); });
	$('#item_location').on("focus", function (e) { setFocus(); });
	$('#incomingitem-quantity').on("focus", function (e) { setFocus(); });
	$('#incomingitem-discount').on("focus", function (e) { setFocus(); });
	$('#incomingitem-price').on("focus", function (e) { setFocus(); });
	$('#incomingitem-gross_price').on("focus", function (e) { setFocus(); });
	$('#incomingitem-subtotal-disp').on("focus", function (e) { setFocus(); });
	$('#A-discount').on("focus", function (e) { setFocus(); });
	$('#A-price').on("focus", function (e) { setFocus(); });
	$('#B-discount').on("focus", function (e) { setFocus(); });
	$('#B-price').on("focus", function (e) { setFocus(); });
	$('#C-discount').on("focus", function (e) { setFocus(); });
	$('#C-price').on("focus", function (e) { setFocus(); });
	
	calculateSubtotal();

	if ($('#item_shortcode').val()) retrieveItem($('#item_shortcode').val());
	// $('.delete-item').click(toAjax);

	/* $('#incomingitem-item_id').on("change", function(e) { 
		$.post("?r=incoming-purchase/get-item", {item_id : this.value}, function(response) {
			$('#unitofmeasurement-label').html('('+response.unit_of_measurement+')');
			$('#incomingitem-quantity').focus();
		}, "json");
	}); */

	$('#item_shortcode').on("change", function (e) {
		retrieveItem(this.value);
	});

	/* $('#incomingitem-item_id').on("select2:unselecting", function(e) { 
		$('#incomingitem-quantity').val(0).change();
		$('#incomingitem-price').val(0);
	}); */

	$('#incomingitem-quantity').on("keyup", function(e) {
		calculateSubtotal();
	});

	$('#incomingitem-price').on("keyup", function (e) {
		calculateSubtotal();
	});

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

	function calculateSubtotal() {
		price 		= $('#incomingitem-price').val();
		quantity 	= $('#incomingitem-quantity').val();		
		subtotal 	= (price * quantity);
		if (subtotal == 0) subtotal = "";
		$('#incomingitem-subtotal').val(Math.round(subtotal));
	}

	$('#incomingitem-subtotal').on("keyup", function (e) {
		calculatePrice();
	});
	function calculatePrice() {
		subtotal 	= $('#incomingitem-subtotal').val();
		quantity 	= $('#incomingitem-quantity').val();
		price 		= (subtotal / quantity);
		$('#incomingitem-price').val(Math.round(price));
	}



	$('#btn-add').on("click", function (e) {
		$(this).prop('disabled', 'disabled');
		formData = $('#form-update-ajax').serialize();
		$('#errorbox').hide();
		incoming_id = $('#incomingitem-incoming_id').val();

		$.post("?r=incoming-purchase/update-ajax&id="+incoming_id, formData, function (response) {
			if (response.success) {
				$.pjax.reload({container:'#grid'}).done(function () {
					$.pjax.reload({container:'#grid-item-search'});
				});
				$('#form-update-ajax').trigger('reset');
				$('#item_shortcode').focus();
				$('.text-total').html(response.total);
				calculateSubtotal();
			} else {
				$('#errorbox').html(response.message.join('<br>'));
				$('#errorbox').fadeIn();
			}
			$('#btn-add').removeAttr('disabled');	
		}, "json");
	});

	$('#btn-edit').on("click", function (e) {
		$(this).prop('disabled', 'disabled');
		formData = $('#form-update-ajax').serialize();
		$('#errorbox').hide();
		incoming_id = $('#incomingitem-incoming_id').val();
		incoming_item_id = $('#incomingitem-id').val();
		
		$.post("?r=incoming-purchase/update-ajax&id="+incoming_id+"&incoming_item_id="+incoming_item_id, formData, function (response) {
			if (response.success) {
				href = $('#cancel').attr('href');
      			window.location.href = href;
			} else {
				$('#errorbox').html(response.message.join('<br>'));
				$('#errorbox').fadeIn();
				$('#btn-edit').removeAttr('disabled');
			}
		}, "json");
	});
});

$('#myModal').on('hidden.bs.modal', function () {
	if ($('#item_shortcode').val() == '') {
		$('#item_shortcode').focus();
	} else {
		$('#incomingitem-quantity').focus();
	}
	$('#myModal input').val('');
	var e = $.Event( "keyup", { keyCode: 13 } );
	$('#myModal input').first().trigger(e);
});
$('#myModal').on('shown.bs.modal', function () {
	$('#myModal input').first().focus();
});

$(document).keydown(function(e) {
	if (e.keyCode == 112 ) { // F1
		console.log(e);
        e.preventDefault();
        $('#myModal').modal('show');
    }
});



$('.panel').on('hidden.bs.collapse', function (e) {
    collapseIn = 0;
	console.log(collapseIn);
})
$('.panel').on('shown.bs.collapse', function (e) {
    collapseIn = 1;
	console.log(collapseIn);
})

$('#item_shortcode').on("focus", function (e) {
	$('.collapse').collapse('hide')
});