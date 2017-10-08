$(document).ready(function(){
	$('#cadastro button').click(function(){
		$('#cadastro').submit();
		$(this).prop('disabled', true);
	});
});