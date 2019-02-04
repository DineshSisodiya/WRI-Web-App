

$(document).ready(function(){
	var hideBodyOverflow=false;
	$('.menu-icon').on('click',function(){
		$('.overlay').toggleClass('show');
		if(hideBodyOverflow==false) {
			$('body').css('overflow','hidden');
			hideBodyOverflow=true;
		} else {
			$('body').css('overflow','auto');
		}
	});
})

