<?
echo <<<HTML

			<footer>{$footer} {$userpanel} </footer>

		</div>
		<!-- 
			* All external libraries used inside the application 
			* core.libs.js includes: underscore, jquery, sizzle, jquery ui 
		-->
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
		<script type="text/javascript" charset="utf-8">
		  var is_ssl = ("https:" == document.location.protocol);
		  var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";
		  document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript" charset="utf-8">
		  var feedback_widget_options = {};
		  feedback_widget_options.display = "overlay";  
		  feedback_widget_options.company = "cmdtask";
		  feedback_widget_options.placement = "left";
		  feedback_widget_options.color = "#222";
		  feedback_widget_options.style = "idea";
		  var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);
		</script>
		{$analytics}
	</body>
</html>
HTML;
?>