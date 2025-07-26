var gross_price = 0;
var collapseIn = 0;

function retrieveItem(shortcode, customer_id, customer_group) {
	gross_price = 0;

	$('.modal').modal('hide');
	isNewRecord = $('#is_new_record').val();
	$.post("?r=outgoing-sale/get-item-by-shortcode", { item_shortcode: shortcode, customer_id: customer_id }, function (response) {

		if (response.unit_of_measurement) {
			$('#unitofmeasurement-label').html('&nbsp;<small>(' + response.current_quantity + ' ' + response.unit_of_measurement + ')</small>');
		} else {
			$('#unitofmeasurement-label').html('');
		}

		$('#item_shortcode').val(response.shortcode);
		$('#item_name').val(response.name);
		$('#item_brand').val(response.brand);
		$('#item_type').val(response.type);

		$('#outgoingitem-item_id').val(response.id);

		if (isNewRecord) {
			$('#outgoingitem-price').val(Math.round(response.purchase_gross_price));
			$('#outgoingitem-price-disp').val(Math.round(response.purchase_gross_price));
			$('#outgoingitem-discount').val(0);
		}

		// $('#last_price').val(formatMoney(response.lastPrice, 0));
		$('#last_price').html(formatMoney(response.lastPrice, 0));
		// $('#base_price').val(formatMoney(response.basePrice, 0));
		$('#base_price').html(formatMoney(response.basePrice, 0));
		
		$('#net_price').html(formatMoney(response.purchase_net_price, 0));
		$('#gross_price').html(formatMoney(response.purchase_gross_price, 0));
		gross_price = response.purchase_gross_price;

		$('.price-groups').val('');

		response.price_groups.forEach(price_group => {
			if (customer_group == price_group.priceGroup.name && isNewRecord) {
				$('#outgoingitem-price').val(Math.round(price_group.price));
				$('#outgoingitem-price-disp').val(Math.round(price_group.price));
				$('#outgoingitem-discount').val(parseFloat(price_group.discount).toFixed(2));
			}
			$('#' + price_group.priceGroup.name + '-discount').html(price_group.discount+'%');
			$('#' + price_group.priceGroup.name + '-price').html(formatMoney(price_group.price, 0));
		});

		if (response.lastPrice && isNewRecord) $('#outgoingitem-price').val(Math.round(response.lastPrice));
		if (response.lastPrice && isNewRecord) $('#outgoingitem-price-disp').val(Math.round(response.lastPrice));
		if (response.lastDiscount && isNewRecord) $('#outgoingitem-discount').val(parseFloat(response.lastDiscount).toFixed(2));

		$('#outgoingitem-quantity').focus();

		if (response.isFound) { 
			$('#outgoingitem-quantity').focus();
		} else {
			$('#item_shortcode').focus();
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
	$('#outgoingitem-quantity').on("focus", function (e) { setFocus(); });
	$('#outgoingitem-discount').on("focus", function (e) { setFocus(); });
	$('#outgoingitem-price-disp').on("focus", function (e) { setFocus(); });
	$('#outgoingitem-subtotal-disp').on("focus", function (e) { setFocus(); });
	
	isNewRecord = $('#is_new_record').val();
	if (!isNewRecord) {
		retrieveItem($('#item_shortcode').val(), $('#outgoing-customer_id').val(), $('#customer_group').val());
	}
	calculateSubtotal();

	if ($('#item_shortcode').val()) retrieveItem($('#item_shortcode').val(), $('#outgoing-customer_id').val(), $('#customer_group').val());

	$('#item_shortcode').on("change", function (e) {
		retrieveItem(this.value, $('#outgoing-customer_id').val(), $('#customer_group').val());
	});
	
	/* $('#outgoingitem-item_id').on("change", function(e) { 
		$.post("?r=outgoing-sale/get-item", { item_id: this.value }, function (response) {
			$('#unitofmeasurement-label').html('(' + response.unit_of_measurement + ')');
			$('#outgoingitem-price').val(response.price);
			$('#outgoingitem-quantity').focus();
		}, "json"); 
	}); */

	/* $('#outgoingitem-item_id').on("select2:unselecting", function(e) { 
		$('#outgoingitem-quantity').val(0).change();
		$('#outgoingitem-price').val(0);
	}); */

	function calculateDiscount() {
		price 		= $('#outgoingitem-price').val();
		discount 	= ((gross_price - price) / gross_price) * 100;
		$('#outgoingitem-discount').val(parseFloat(discount).toFixed(2));
		calculateSubtotal();
	}

	function calculateNetPrice() {
		discount 	= $('#outgoingitem-discount').val();
		price 		= gross_price - (discount/100 * gross_price);
		$('#outgoingitem-price').val(Math.round(price));
		$('#outgoingitem-price-disp').val(Math.round(price));
		calculateSubtotal();
	}

	function calculateSubtotal() {
		price 		= $('#outgoingitem-price').val();
		quantity 	= $('#outgoingitem-quantity').val();		
		subtotal 	= (price * quantity);
		if (subtotal == 0) subtotal = "";
		$('#outgoingitem-subtotal').val(Math.round(subtotal));
		$('#outgoingitem-subtotal-disp').val(Math.round(subtotal));
	}

	function calculatePrice() {
		subtotal 	= $('#outgoingitem-subtotal').val();
		quantity 	= $('#outgoingitem-quantity').val();
		price 		= (subtotal / quantity);
		console.log(price);
		$('#outgoingitem-price').val(Math.round(price));
		$('#outgoingitem-price-disp').val(Math.round(price));
		calculateDiscount();
	}
	
	$('#outgoingitem-quantity').on("keyup", function(e) {
		if ((e.keyCode >= 49 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 173 || e.keyCode == 188 || (e.keyCode >= 190 && e.keyCode <= 192) || (e.keyCode >= 219 && e.keyCode >= 222) || e.keyCode == 8 || e.keyCode == 46) {
			calculateSubtotal();
		}
	});

	$('#outgoingitem-price').on("change", function (e) {
		calculateDiscount();
	});
	$('#outgoingitem-price-disp').on("keyup", function (e) {
		$('#outgoingitem-price').val($(this).inputmask('unmaskedvalue'));
		calculateDiscount();
	});
	
	$('#outgoingitem-discount').on("keyup", function (e) {
		if ((e.keyCode >= 49 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 173 || e.keyCode == 188 || (e.keyCode >= 190 && e.keyCode <= 192) || (e.keyCode >= 219 && e.keyCode >= 222) || e.keyCode == 8 || e.keyCode == 46) {
			calculateNetPrice();
		}
	});
	
	$('#outgoingitem-subtotal').on("change", function (e) {
		calculatePrice();
	});
	$('#outgoingitem-subtotal-disp').on("keyup", function (e) {
		if ((e.keyCode >= 49 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || e.keyCode == 173 || e.keyCode == 188 || (e.keyCode >= 190 && e.keyCode <= 192) || (e.keyCode >= 219 && e.keyCode >= 222) || e.keyCode == 8 || e.keyCode == 46) {
			$('#outgoingitem-subtotal').val($(this).inputmask('unmaskedvalue'));
			calculatePrice();
		}
	});

	$('#btn-add').on("click", function (e) {
		$(this).prop('disabled', 'disabled');
		formData = $('#form-update-ajax').serialize();
		$('#errorbox').hide();
		outgoing_id = $('#outgoingitem-outgoing_id').val();

		$.post("?r=outgoing-sale/update-ajax&id="+outgoing_id, formData, function (response) {
			if (response.success) {
				$.pjax.reload({container:'#grid'}).done(function () {
					$.pjax.reload({container:'#grid-item-search'}).done(function () {
						$.pjax.reload({container:'#grid-item-search-2'});
					});
				});
				$('#form-update-ajax').trigger('reset');
				$('#item_shortcode').focus();
				$('#unitofmeasurement-label').html('');
				$('.text-total').html(response.total);
				calculateSubtotal();
			} else {
				try {
					$('#errorbox').html(response.message.join('<br>'));
				} catch {
					$('#errorbox').html(response.message);
				}
				$('#errorbox').fadeIn();
			}
			$('#btn-add').removeAttr('disabled');
		}, "json");
	});

	$('#btn-edit').on("click", function (e) {
		$(this).prop('disabled', 'disabled');
		formData = $('#form-update-ajax').serialize();
		$('#errorbox').hide();
		outgoing_id = $('#outgoingitem-outgoing_id').val();
		outgoing_item_id = $('#outgoingitem-id').val();
		
		$.post("?r=outgoing-sale/update-ajax&id="+outgoing_id+"&outgoing_item_id="+outgoing_item_id, formData, function (response) {
			if (response.success) {
				href = $('#cancel').attr('href');
      			window.location.href = href;
			} else {
				try {
					$('#errorbox').html(response.message.join('<br>'));
				} catch {
					$('#errorbox').html(response.message);
				}
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

$('#myModal2').on('hidden.bs.modal', function () {
	if ($('#item_shortcode').val() == '') {
		$('#item_shortcode').focus();
	} else {
		$('#incomingitem-quantity').focus();
	}
	$('#myModal2 input').val('');
	var e = $.Event( "keyup", { keyCode: 13 } );
	$('#myModal2 input').first().trigger(e);
});
$('#myModal2').on('shown.bs.modal', function () {
	$('#myModal2 input').first().focus();
});

$(document).keydown(function(e) {
	if (e.keyCode == 112 ) { // F1
		console.log(e);
        e.preventDefault();
        $('#myModal2').modal('hide');
        $('#myModal').modal('show');
	}	
	if (e.keyCode == 113 ) { // F2
		console.log(e);
        e.preventDefault();
        $('#myModal2').modal('show');
        $('#myModal').modal('hide');
    }
});



$('#outgoing-customer_id').on("change", function(e) { 
	$.post("?r=outgoing-sale/get-customer", { customer_id: this.value }, function (response) {
		$('#outgoing-salesman_id').val(response.salesman_id).change();
		// $('#outgoingitem-quantity').focus();
	}, "json"); 
});

$('#outgoing-customer_id').on("select2:unselecting", function(e) { 
	$('#outgoing-salesman_id').val(0).change();
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