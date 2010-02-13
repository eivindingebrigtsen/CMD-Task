                              	$.dateReplace = function(val){
 $.ajax({
			  url: 'tasks/date/'+val,
			  dataType: 'json',
			  type: 'POST',
			  success: function(data,status){
				if(data.status === 'success'){
					$('.date_replace')
						.text(data.date)
						.removeClass('date_replace')
						.addClass('date');							
				}else{ $('.date_replace').removeClass('date_replace');
				}					    
			}});
		
	};
	$.datesReplace = function(arr){
 $.ajax({
			  url: 'tasks/dates/',
			  data: {
				"dates": arr
			  },
			  dataType: 'json',
			  type: 'POST',
			  success: function(data,status){
				if(data.status === 'success'){
 $('.date_replace').each(function(i,v){
 $(this)
						.text(data.dates[i])
						.removeClass('date_replace');
						
					});
				}else{ $('.date_replace').removeClass('date_replace');
				}					    
			}});
		
	};
	function replaceSyntax(txt){
		console.log(txt);
		var datematch = /(mon|tue|wed|thu|fri|sat|sun|sunday|monday|tuesday|wednesday|thursday|friday|saturday|yesterday|tomorrow|today|now|\+[0-9]\sday|\+[0-9]\sweek|\+[0-9]\smonth|\+[0-9]\syear|((31(?! (FEB|APR|JUN|SEP|NOV)))|((30|29)(?! FEB))|(29(?= FEB (((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))|(0?[1-9])|1\d|2[0-8]) (JAN|FEB|MAR|MAY|APR|JUL|JUN|AUG|OCT|SEP|NOV|DEC) ((1[6-9]|[2-9]\d)\d{2}))/gi;
		var dates = txt.match(datematch, function(a,b,c,d){
			console.log('MATCH: ', a, b, c, d);
		});
		if(dates!==null){
			if(dates.length === 1){						
 				 $.dateReplace(dates[0]);
				txt = txt.replace(datematch,'<span class="date date_replace">$1</span>');
			}else{
			var datearr = [];
				_.each(dates, function(d,v){
					datearr.push(d);								
				}); 
				$.datesReplace(datearr);
				txt = txt.replace(datematch,function(a,b,c,d){
					return '<span class="date date_replace">'+a+'</span>';
				}); 
				console.log(datearr);
			}
	   	}
		txt = txt.replace(/(?:^|\n)(\*(^\/)|\•|\-)(.*)(?=^\n|\r|\@|\||\#|\/\*|\*\/)/g, function(a,b,c){
			console.log('a', a, 'b', b, 'c',c);
			return '<h3 class="title">'+a+'</h3>';
		});
		txt = txt.replace(/\@(\w{1,64})/g, 		'<span class="keyword contexts">@$1</span>');
		txt = txt.replace(/\|(\w{1,64})/g, 		'<span class="keyword projects">|$1</span>'); 
		txt = txt.replace(/\#(\w{1,64})/g, 		'<span class="keyword tags">#$1</span>'); 
		txt = txt.replace(/\/\*[\d\D]*?\*\//g,'<p class="task-desc">$&</p>'); 
		return txt;
	};
$('#textarea').bind('blur', function(e){
		e.preventDefault();
		var he = $(this);
		var txt = he.text();
	 $.ajax({
				  url: 'tasks/interpret/',
				  data: {
					"raw": txt
				  },
				  dataType: 'json',
				  type: 'POST',
				  success: function(data,status){
					console.log(data,status)
				}});


//		txt = replaceSyntax(txt);
//		me.html(txt).focus();				
	});
$('#editable').focus();