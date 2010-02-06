		$(function(){
			$(document).data('codes', []);
			{$codes}
			$('body').siteSearch();
			function doGetCaretPosition (ctrl) {
				var CaretPos = 0;	// IE Support
				if (document.selection) {
				ctrl.focus ();
					var Sel = document.selection.createRange ();
					Sel.moveStart ('character', -ctrl.value.length);
					CaretPos = Sel.text.length;
				}
				// Firefox support
				else if (ctrl.selectionStart || ctrl.selectionStart == '0')
					CaretPos = ctrl.selectionStart;
				return (CaretPos);
			}
			function setCaretPosition(ctrl, pos){
				if(ctrl.setSelectionRange)
				{
					ctrl.focus();
					ctrl.setSelectionRange(pos,pos);
				}
				else if (ctrl.createTextRange) {
					var range = ctrl.createTextRange();
					range.collapse(true);
					range.moveEnd('character', pos);
					range.moveStart('character', pos);
					range.select();
				}
			}

			function keyWordCheck(val, me){
				var dates = val.match(/^(mon|tue|wed|thu|fri|sat|sun|sunday|monday|tuesday|wednesday|thursday|friday|saturday|yesterday|tomorrow|today|now|\+[0-9]\sday|\+[0-9]\sweek|\+[0-9]\smonth)$/i);
				if(dates){
					me.addClass('date_replace');
					$.ajax({
					  url: 'tasker/date/'+val.replace('_', ' '),
					  dataType: 'json',
					  type: 'POST',
					  success: function(data,status){
						if(data.status === 'success'){
							$('.date_replace')
								.text(data.date)
								.removeClass('date_replace')
								.addClass('date');							
						}else{
						    $('.date_replace').removeClass('date_replace');
						}					    
					}});
				}
				var email = val.match(/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/)
					if(email){
						me.addClass('email')
					}
				var url = val.match(/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/);
					//val.match(/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/)
					if(url){
						me.addClass('link')
					}
				var hex = val.match(/^#?([a-f0-9]{6}|[a-f0-9]{3})$/)
					if(hex){
						me.addClass('hex')
					}
			};
			function addTask(){
				if(!$.loading){				
				me = $('#input');
				keywords = [];
				var all = ''; 
				me.find('li').text(function(i,t){
					all += t+ ' ';
				});
				me.find('li.keyword').each(function(){
					$(this).removeClass('keyword');
			 		keywords.push([$(this).attr('class'), $(this).text().substr(1)]);
					$(this).addClass('keyword');
				});                                         
				var text = '';
				me.find('li:not(.keyword,.desc,#holder,.date)').each(function(){
					if($(this).text()){
						text += $(this).text()+' ';						
					}
				});
				var date = me.find('li.date').text();
				var desc = all.match(/\*.*?\*/);
				console.log('DESC', desc);
				var params = {};
				if (all) 		params.raw = all;
				if (keywords)	params.keys = keywords;
				if (text)		params.text = text;
				if (desc)		params.desc = desc[0];
				if (date)		params.date = date;
				console.log('sending up:', params);
				$.loading = true;
				$.ajax({
				  url: 'tasker/add',
				  dataType: 'json',
				  type: 'POST',
				  data: params,
				  success: function(data,status){
					$[data.status](data.string);
					$.loading = false;
					$('aside').empty().load('tasker/keys', function(){
						$('#search_list').trigger('listUpdate');
					});
					$('section.content').empty().load('tasker/tasks');
					$('#input').find('li:not(#holder)').hide(200, function(){ $(this).remove()});
					$('#insert').val('');
					
				}});
			}
				//console.log('Addtask', params);
			}; 
			function handleFirstKeys(val, ins, first){  
				$(document).data('desc', false);
				var type;
				switch(val){
					{$handleshortcuts}
				}
				if (type){
					$(document).data('keyword', true);					
				}
			};
			function handleSecondKeys(val, ins){
				//console.log('handling')
			   if(val==='/\*'){     
					$(document).data('desc', true);
					ins.addClass('desc').focus();
				}									
			}; 
			function handleEscape(ins,val){
				/**
				 *  @todo fix the description escapes 
				 *  @todo look at moving some of these functions to PHP
				**/
				
				$(document).data('keyword', false);
				var arr = val.split(' ');
				if(arr.length>1){
					var desc = ($('#input').find('.desc').length ? $('#input').find('.desc') : $('<li class="desc"></li>'));
					var txt = [];
					_.each(arr, function(v,k){
						if(v!==''){
							var item = $('<li>'+v+'</li>');
							handleFirstKeys(v.substr(0,1), item); 
							handleSecondKeys(v.substr(0,2), item);
							keyWordCheck(v, item);						
							if( $(document).data('desc')){								
									txt.push(desc.text()); 
							}else{
								item.appendTo('#input');																
							}
						}
					});
					desc.html(txt.join(' ')).appendTo('#input');
					//console.log(val.length,doGetCaretPosition(this),arr);
				}else{
					if(!ins.hasClass('.keyword')){
						handleFirstKeys(val.substr(0,1), ins);
						keyWordCheck(val, ins);						
					}					
					$('<li>'+val+'</li>')
						.addClass(ins.attr('class'))
						.appendTo('#input');
				}     
				console.log()
				ins.removeAttr('value')
					.removeAttr('class')
					.parent()
					.appendTo('#input');
				ins.focus();
			}
			$('.task-done.done').live('click', function(ev){
				me = $(this);
			   	$.ajax({
				  url: 'tasker/undo',
				  dataType: 'json',
				  type: 'POST',
				  data: {id : me.siblings('input').val()},
				  success: function(data,status){
					//console.log(data,status)
					if(data.status==="success"){
						me.closest('li.task').andSelf().removeClass('done').addClass('todo');
					}
				}});
			});

			$('.task-done.todo').live('click', function(ev){
				me = $(this);
			   	$.ajax({
				  url: 'tasker/do',
				  dataType: 'json',
				  type: 'POST',
				  data: {id : me.siblings('input').val()},
				  success: function(data,status){
					//console.log(data,status)
					if(data.status==="success"){
						me.closest('li.task').andSelf().removeClass('todo').addClass('done');
					}
				}});
			});

			$('.task-delete').live('click', function(ev){
				me = $(this);
			   	$.ajax({
				  url: 'tasker/delete',
				  dataType: 'json',
				  type: 'POST',
				  data: {id : me.siblings('input').val()},
				  success: function(data,status){
					//console.log(data,status)
					if(data.status==="success"){
						me.closest('li.task').hide(200, function(){
							$(this).remove();
						});
					}
				}});
			});
/*			$('.task', '#tasks').live('dblclick', function(ev){
				var task = $(this);
				if(! task.is('.editing')){
					task.addClass('editing');
					var id = task.find('input').val(),
					raw = task.find('.task-raw').text();
					task.children('div').hide(200);
					$('#input')
					
					$('#input').find('li:not(#holder)').remove();
					$('#insert').val(raw).attr('rel', id);					
					handleEscape($('#insert'), raw);
				}
			}); */
			// $('textarea', '#tasks').live('focusout', function(ev){
			// 	var task = $(this).parent();
			// 	if(task.find('.task-raw').text() === $(this).val()){
			// 		$(this).siblings().show(300);
			// 		$(this).remove()
			// 		task.removeClass('editing');
			// 	}else{
			// 		console.log( 'Diff' )				
			// 	}
			// });
			$('#input').bind('click', function(){
				$('#insert').focus();
			})

			$(document).data('desc', false)
			$('#insert').attr('autocomplete', 'off');
			$('#insert').bind('keyup', {combi:'ctrl+a', disableInInput: false}, function(){
				var nodes = $('#input').find('li:not(#holder)');
				var txt = '';
				nodes.each(function(){
					if($(this).text()!== 'undefined'){
						txt += $(this).text()+' ';						
					}
				});
				var val = $(this).val();
				$(this).val(txt+' '+val);
				nodes.remove();
			});

			$('#insert').live('keyup', function(ev){
				// HANDLE RETURN
				if(ev.keyCode === 9 || ev.keyCode === 13){
					ev.preventDefault();
				}
				var ins = $(this);
				var val = $.trim(ins.val());    
				
				if(val.length>10){
					ins.css('width', '750px');
				}else{
					ins.css('width', 'auto');
				}
				
				
				// Handle delete
				if(ev.keyCode  === 8 && val === ''){
					var index = $('li').index(ins.parent());
					ins.val(ins.parent().prev().text());
					ins.removeAttr('class').parent().prev().remove();
				  	return true;
					//console.log(index);
				}
				// Handle Back arrow at pos 0
				if(ev.keyCode  === 37 && doGetCaretPosition(this) === 0){
					var index = $('li').index(ins.parent());
					var prevt = ins.parent().prev().text();
					var pos = prevt.length+1;
					ins.val(prevt+' '+val);
					ins.removeAttr('class').parent().prev().remove();
					setCaretPosition(this, pos);
				    return true;
				  //console.log(index, pos, prevt, val, prevt.length);
				}


				// Handle escaping the input 
				// SPACE AND TYPING KEYWORD
				if((ev.keyCode === 32 && !$(document).data('desc'))){
					handleEscape(ins,val);
					return true;
				  
				}                     
				
				if(ev.keyCode === 13){
					handleEscape(ins,val);
				  
				}

				if((ev.keyCode === 188 && !$(document).data('desc'))){
					handleEscape(ins,val);
					ins.val(' ');
					return true;
				  
				}
				if(ev.ctrlKey && ev.keyCode  === 13 && !$.loading){
				  var wait = handleEscape(ins,val);
				  addTask(ins);
				  return true;
				}
  
  				if(val.length===1 && !$(document).data('desc')){					
					handleFirstKeys(val, ins, true);
				  	return true;
				}
				if(val.length===2){
					handleSecondKeys(val, ins);
				  	return true;
				}
			    //console.log('KEYCODE', ev.keyCode, 'VAL L', val.length)
			});
			$('#insert').focus();
			$('.task-keywords li, aside li').live('click',function(ev){
				$('#tasks > li > ul').find('li:not(.task-keywords li)').hide();
				$('#tasks > li > ul').find('li.'+$(this).attr('rel')).show().parents('li').show();
			}); 
			$.dateReplace = function(val){
				  $.ajax({
					  url: 'tasker/date/'+val,
					  dataType: 'json',
					  type: 'POST',
					  success: function(data,status){
						if(data.status === 'success'){
							$('.date_replace')
								.text(data.date)
								.removeClass('date_replace')
								.addClass('date');							
						}else{
						    $('.date_replace').removeClass('date_replace');
						}					    
					}});
				
			}
			$.datesReplace = function(arr){
				  $.ajax({
					  url: 'tasker/dates/',
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
								
							})
						}else{
						    $('.date_replace').removeClass('date_replace');
						}					    
					}});
				
			}
			function replaceSyntax(txt){
				var datematch = /(mon|tue|wed|thu|fri|sat|sun|sunday|monday|tuesday|wednesday|thursday|friday|saturday|yesterday|tomorrow|today|now|\+[0-9]\sday|\+[0-9]\sweek|\+[0-9]\smonth|\+[0-9]\syear|((31(?! (FEB|APR|JUN|SEP|NOV)))|((30|29)(?! FEB))|(29(?= FEB (((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00)))))|(0?[1-9])|1\d|2[0-8]) (JAN|FEB|MAR|MAY|APR|JUL|JUN|AUG|OCT|SEP|NOV|DEC) ((1[6-9]|[2-9]\d)\d{2}))/gi;
				var dates = txt.match(datematch, function(a,b,c,d){
					console.log('MATCH: ', a, b, c, d);
				});
				if(dates!=null){
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
				txt = txt.replace(/(?:^|\\n)(\*(^\/)|\•|\-)(.*)(^\\n|\\r|\@|\||\#|\/\*|\*\/)/g, function(a,b,c){
					console.log('a', a, 'b', b, 'c',c)
					return '<h3 class="title">'+a+'</h3>'
				});
				txt = txt.replace(/\@(\w{1,64})/g, 		'<span class="keyword contexts">@$1</span>'); //Context
				txt = txt.replace(/\|(\w{1,64})/g, 		'<span class="keyword projects">|$1</span>'); // Project
				txt = txt.replace(/\#(\w{1,64})/g, 		'<span class="keyword tags">#$1</span>'); // Tag
				txt = txt.replace(/\/\*[\d\D]*?\*\//g,'<p class="task-desc">$&</p>');  // Multiline comment
				return txt;
			};

		   $('#editable').focus();
		   $('#editable').bind('blur', function(e){
				e.preventDefault();
				var me = $('#editable');
				var txt = me.text();
				txt = replaceSyntax(txt);
				me.html(txt).focus();				
			});
			
			 			
		})