<?
echo <<<HTML
	<!DOCTYPE html>
	<html>
		<head>           
			<base href="{$base}">
			<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
			<title>{$title}</title>
			<link rel="stylesheet" href="static/css/master.css" type="text/css" media="screen" title="Yes we are stylish" charset="utf-8">
			<!--[if IE]>
			<script type="text/javascript" charset="utf-8">
				// ENABLING HTML5 in IE
				// For discussion and comments, see: http://remysharp.com/2009/01/07/html5-enabling-script/
				(function(){if(!/*@cc_on!@*/0)return;var e = "abbr,article,aside,audio,canvas,datalist,details,eventsource,figure,footer,header,hgroup,mark,menu,meter,nav,output,progress,section,time,video".split(','),i=e.length;while(i--){document.createElement(e[i])}})()
			</script>
			<![endif]-->
		</head>
		<body>
			<div class="wrapper {$action}">
			<header>{$header}</header>
HTML;
?>