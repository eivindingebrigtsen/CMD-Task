+Task	
=========================
Real Simple Task Management. +Task is an online list manager, that aims to be ubiquitous in terms of sources of inputs.

Syntax:
------------------------
    - A task item that needs to be in some list http://urlthatyouneedtostore.com 
    @context_or_person /list ?project #topic :remind & _deadline(tomorrow, friday, +3 weeks)
    // Single line comment or a
    /* Multiline
       Comment */

Dependencies:
-------------------------
- Oauth 		  		=> Authentification
- PHP 5.2			=> Serverside
- MySQL				=> Database (will be ported to more at a later stage)
- Scaffold css		=> for easier css writing and skinning 
- jQuery		 		=> javascript framework
- FirePHP			=> output and debugging


Installing:
------------------------
- Configuration of the system in /etc/configuration.ini  
- SQL for the database is in /etc/db.sql
- Go to your http://path/to/cmd-task/tasks
- Secret is abc 


Online Site index:
------------------------
- http://server/login 
- http://server/tasks 
- http://server/tasks/list 		<= gets all items for that list
- http://server/tasks/|@context <= gets all tasks for that context
- http://server/tasks/|project  <= gets all tasks for that project
- http://server/tasks/_time		<= gets all tasks for that day
- http://server/tasks/#topic  	<= gets all items for that topic


Planned Features:
------------------------
- Calendar subscription
- Bookmarklet
- Email tasks service
- RSS output
- jqTouch version
- Instapaper integration
- API 

Inspired by:
------------------------
- David Allen 	http://www.davidco.com/
- 37Signals 	http://gettingreal.37signals.com/
- Instapaper 	http://instapaper.com
