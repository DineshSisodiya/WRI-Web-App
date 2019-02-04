

$(document).ready(function(){

	// sidebar menu toggle
	$('.menu-icon').on('click',function() {
		$('.menu_section').toggleClass("show");
	});

	// add person step-3  
	$('select[name="payment_mode"]').on('change', function() {
		// alert( this.value );
		if(this.value!=="cash") {
			$('div[id="transaction_id"]').remove();
			$('div[id="payment_mode"]').after('<div class="form-group" id="transaction_id"><label class="control-label">'+ $("#payment_mode_op option:selected").text() +' Transaction ID</label><input name="transaction_id" class="form-control"  placeholder="Last 5 digit for online payment" type="number" min="0" required></div>');
		} else {
			$('div[id="transaction_id"]').remove();
		}
		
	});

	// add donor page step 2
	$('input[name="pickWtspNmbr"]').on('change',function() {
		if($(this).prop("checked") == true){
			if($('input[name="mobile"]').val())
				$('input[name="whatsapp"]').val($('input[name="mobile"]').val());
			else {
				$(this).prop("checked",false);
				alert('First enter Mobile number');
			}
		} else {
			$('input[name="whatsapp"]').val('');
		}
	});

});