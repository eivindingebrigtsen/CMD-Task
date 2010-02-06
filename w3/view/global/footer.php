<?
echo <<<HTML

			<footer>{$footer} {$userpanel} </footer>

		</div>
		<!-- 
			* All external libraries used inside the application 
			* core.libs.js includes: underscore, jquery, sizzle, jquery ui 
		-->
		<script src="static/javascript/core.libs.js" type="text/javascript" charset="utf-8"></script>       
		<script src="static/javascript/core.libs.js" type="text/javascript" charset="utf-8"></script>       
		<!-- 
			* Internal extensions used inside the application 
			* core.ext.js includes: i18n, utilities 
		-->
		<script src="static/javascript/core.ext.js" type="text/javascript" charset="utf-8"></script>       
		<script type="text/javascript" charset="utf-8">
			$(function(){        
				// Start DomReady  
				{$inlinejs}

				// End DomReady
			}); 
		</script>  
		{$analytics}
	</body>
</html>
HTML;
?>