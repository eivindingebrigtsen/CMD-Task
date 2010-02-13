$.sendPass = function(){
$.post('admin/login', {phrase: $('#phrase').val()}, function(data,status){
		var js = eval('(' + data + ')');
		if(js.status === 'success'){
			 window.location = 'tasks/';
		}
	});			
};
$('#phrase').keyup(function(e){
	clearTimeout($(document).data('timout'));
	$(document).data('timout', setTimeout(function(){
	    $.sendPass();
	}, 300));
	if(e.keyCode===13){
		$.sendPass();
	}
}); 