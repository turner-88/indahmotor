
function retrieveItem(shortcode) {
	$('.modal').modal('hide');
	isNewRecord = $('#is_new_record').val();
	$.post("?r=order/get-item-by-shortcode", { item_shortcode: shortcode }, function (response) {

		if (isNewRecord) {
			$('#orderitem-item_shortcode').val(response.shortcode);
			$('#orderitem-item_name').val(response.name);
			$('#orderitem-brand_storage').val(response.brand);
			$('#orderitem-brand_supplier').val(response.brand);
			$('#orderitem-type').val(response.type);
			$('#orderitem-unit_of_measurement').val(response.unit_of_measurement);
		}

		$('#net_price').val(response.purchase_net_price);
		$('#subtotal').html(formatMoney(response.purchase_net_price * $('#orderitem-quantity').val(), 0));
		
		$('#current_quantity').html(response.current_quantity);
		$('#total_order_quantity').html(response.total_order_quantity);
		$('#total_to_be_ordered').html(response.total_to_be_ordered);

		$('#orderitem-quantity').focus();

		if (response.isFound) { 
			$('#orderitem-quantity').focus();
		} else {
			$('#orderitem-item_shortcode').focus();
		}
	}, "json");
}

function retrieveTotalToBeOrdered(shortcode, supplier_id) {
	$.post("?r=order/get-total-to-be-ordered", { item_shortcode: shortcode, supplier_id: supplier_id }, function (response) {
		$('#total_to_be_ordered_quantity').html(response.total_to_be_ordered_quantity);
		$('#total_to_be_ordered_value').html(formatMoney(response.total_to_be_ordered_value, 0));
	}, "json");
}


$(document).ready(function() {
	isNewRecord = $('#is_new_record').val();
	if (!isNewRecord) {
		retrieveItem($('#orderitem-item_shortcode').val());
	}
	calculateSubtotal();

	if ($('#orderitem-item_shortcode').val()) retrieveItem($('#orderitem-item_shortcode').val());

	$('#orderitem-item_shortcode').on("change", function (e) {
		retrieveItem(this.value);
	});

	$('#orderitem-supplier_id').on("change", function (e) {
		retrieveTotalToBeOrdered($('#orderitem-item_shortcode').val(), this.value);
	});
	
	/* $('#orderitem-item_id').on("change", function(e) { 
		$.post("?r=outgoing-sale/get-item", { item_id: this.value }, function (response) {
			$('#unitofmeasurement-label').html('(' + response.unit_of_measurement + ')');
			$('#orderitem-price').val(response.price);
			$('#orderitem-quantity').focus();
		}, "json"); 
	}); */

	$('#orderitem-item_id').on("select2:unselecting", function(e) { 
		$('#orderitem-quantity').val(0).change();
		$('#orderitem-price').val(0);
	});

	$('#orderitem-quantity').on("keyup", function(e) {
		if ($('#orderitem-to_be_ordered').val() == '') $('#orderitem-to_be_ordered').val($('#orderitem-quantity').val());
		calculateSubtotal();
	});

	function calculateSubtotal() {
		price 		= $('#net_price').val();
		quantity 	= $('#orderitem-quantity').val();	
		subtotal 	= (price * quantity);
		if (subtotal == 0) subtotal = "";
		$('#subtotal').html(formatMoney(subtotal, 0));
	}

	$('#btn-add').on("click", function (e) {
		$(this).prop('disabled', 'disabled');
		formData = $('#form-update-ajax').serialize();
		$('#errorbox').hide();
		order_id = $('#orderitem-order_id').val();

		$.post("?r=order/update-ajax&id="+order_id, formData, function (response) {
			if (response.success) {
				$.pjax.reload({container:'#grid'}).done(function () {
					$.pjax.reload({container:'#grid-item-search'});
				});
				$('#form-update-ajax').trigger('reset');
				$('#orderitem-item_shortcode').focus();
				$('#text-total').html(response.total);
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
		order_id = $('#orderitem-order_id').val();
		order_item_id = $('#orderitem-id').val();
		
		$.post("?r=order/update-ajax&id="+order_id+"&order_item_id="+order_item_id, formData, function (response) {
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
	$('#orderitem-quantity').focus();
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
