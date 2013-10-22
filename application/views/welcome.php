<p>An easy way to access data via the GCM website</p>

<h3>Examples</h3>

<ul>
	<li><a href="<?php echo site_url('events');?>">Events</a> Upcoming CCB Events, HTML Output (Default)</li>
	<li><a href="<?php echo site_url('events?trim=1');?>">Events (embedded)</a> - Same as above, with the header/footer markup trimmed for easy embedding within another page via AJAX</li>
	<li><a href="<?php echo site_url('events/index.xml');?>">Events</a> Get it in XML</li>
	<li><a href="<?php echo site_url('events/index.json');?>">Events</a> Get it in JSON</li>
</ul>