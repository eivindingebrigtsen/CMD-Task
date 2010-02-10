
		$(function(){
			$(document).data('codes', []);
			{$codes}
			$('body').siteSearch();
			function doGetCaretPosition (ctrl) {
				var CaretPos = 0;	/*  IE Support */
				if (document.selection) {
				ctrl.focus ();
					var Sel = document.selection.createRange ();
					Sel.moveStart ('character', -ctrl.value.length);
					CaretPos = Sel.text.length;
				}
				/*  Firefox support */
				else if (ctrl.selectionStart || ctrl.selectionStart == '0'){
					CaretPos = ctrl.selectionStart;                        
					}
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
					  url: 'tasks/date/'+val.replace('_', ' '),
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
				var email = val.match(/^([a-z0-9_\.-]+)@([\da-z\.-]+)\.([a-z\.]{2,6})$/);
					if(email){
						me.addClass('email');
					}
				var url = val.match(/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/);
					/* val.match(/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/) */
					if(url){
						me.addClass('link');
					}
				var hex = val.match(/^#?([a-f0-9]{6}|[a-f0-9]{3})$/);
					if(hex){
						me.addClass('hex');
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
				if (all) 		{params.raw = all;}
				if (keywords)	{params.keys = keywords;}
				if (text)		{params.text = text;}
				if (desc)		{params.desc = desc[0];}
				if (date)		{params.date = date;}
				console.log('sending up:', params);
				$.loading = true;
				$.ajax({
				  url: 'tasks/add',
				  dataType: 'json',
				  type: 'POST',
				  data: params,
				  success: function(data,status){
					$[data.status](data.string);
					$.loading = false;
					$('aside').empty().load('tasks/keys', function(){
						$('#search_list').trigger('listUpdate');
					});
					$('section.content').empty().load('tasks/tasks');
					$('#input').find('li:not(#holder)').hide( 200, 
						function(){ 
							$(this).remove();
						});
					$('#insert').val('');
					
				}});
			}
				/* console.log('Addtask', params); */
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
				/* console.log('handling') */
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
				  url: 'tasks/undo',
				  dataType: 'json',
				  type: 'POST',
				  data: {id : me.siblings('input').val()},
				  success: function(data,status){
					/* console.log(data,status) */
					if(data.status==="success"){
						me.closest('li.task').andSelf().removeClass('done').addClass('todo');
					}
				}});
			});

			$('.task-done.todo').live('click', function(ev){
				me = $(this);
			   	$.ajax({
				  url: 'tasks/do',
				  dataType: 'json',
				  type: 'POST',
				  data: {id : me.siblings('input').val()},
				  success: function(data,status){
					/* console.log(data,status) */
					if(data.status==="success"){
						me.closest('li.task').andSelf().removeClass('todo').addClass('done');
					}
				}});
			});

			$('.task-delete').live('click', function(ev){
				me = $(this);
			   	$.ajax({
				  url: 'tasks/delete',
				  dataType: 'json',
				  type: 'POST',
				  data: {id : me.siblings('input').val()},
				  success: function(data,status){
					/* console.log(data,status) */
					if(data.status==="success"){
						me.closest('li.task').hide(200, function(){
							$(this).remove();
						});
					}
				}});
			});

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
				/*  HANDLE RETURN */
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
				
				
				/*  Handle delete */
				if(ev.keyCode  === 8 && val === ''){
					var index = $('li').index(ins.parent());
					ins.val(ins.parent().prev().text());
					ins.removeAttr('class').parent().prev().remove();
				  	return true;
					/* console.log(index); */
				}
				/*  Handle Back arrow at pos 0 */
				if(ev.keyCode  === 37 && doGetCaretPosition(this) === 0){
					var index = $('li').index(ins.parent());
					var prevt = ins.parent().prev().text();
					var pos = prevt.length+1;
					ins.val(prevt+' '+val);
					ins.removeAttr('class').parent().prev().remove();
					setCaretPosition(this, pos);
				    return true;
				  /* console.log(index, pos, prevt, val, prevt.length); */
				}


				/*  Handle escaping the input  */
				/*  SPACE AND TYPING KEYWORD */
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
			    /* console.log('KEYCODE', ev.keyCode, 'VAL L', val.length) */
			});
			$('#insert').focus();
			$('.task-keywords li, aside li').live('click',function(ev){
				window.location.href= '/CMD-Task/tasks/'+$(this).text();

			}); 
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
						}else{
						    $('.date_replace').removeClass('date_replace');
						}					    
					}});
			}
		});