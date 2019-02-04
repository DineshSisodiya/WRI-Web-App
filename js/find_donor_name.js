$(document).ready(function(){
	function get_donor_name(mob) {
		var dataString = {
			mobile:mob
		}
		$.ajax({
			type:'POST',
			url:'./find_donor_name.php',
			dataType: 'json',
      		data: JSON.stringify(dataString),
      		contentType: 'application/json; charset=utf-8',
			success:function(data) {
				$('#responseSet').prop('value',data['success']);
				$('#responseSet').prop('name',data['response']).trigger('change');
			}
		});
	}
	$('#donor_mobile').on('keyup',function(){
		var mob = $('#donor_mobile').val();
		if(mob.length==10) {
			get_donor_name(mob);
		} else {
			$('#reg_donor_name').remove();
		}
	});
	$('#responseSet').on('change',function(){
		if ($('#responseSet').val()==="true") {
			$('#reg_donor_name').remove();
			$('#mobile_verify').append('<label id="reg_donor_name" style="color:green;">Donor Name : '+$('#responseSet').prop('name')+'</label>');
		} else {
			$('#reg_donor_name').remove();
			$('#mobile_verify').append('<label id="reg_donor_name" style="color:red;">Donor Mobile Number not registered</label>');
		}
	});
});
