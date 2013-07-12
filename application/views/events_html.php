<?php if ($full_page): ?>
	<!DOCTYPE html>
	<html>
		<head>
			<meta http-equiv="content-type" content="text/html; charset=UTF-8" />	
			<title><?php echo $title; ?></title>
		</head>
		<body>
			<h1><?php echo $title; ?></h1>
<?php endif; ?>

<ul class="events-wrapper">
	<?php foreach ($items as $item): ?>
		<li class="date">
			<h4 class="date-header"><?php echo $item['date_string']; ?></h4>
			<ul class="events">
				<?php foreach ($item['events'] as $event): ?>
					<li class="event">
						<span class="event-name"><?php echo $event['event_name']; ?></span>
						<?php if ($event['short_time_string'] !== ''): ?>
							<span class="event-time"><?php echo $event['short_time_string']; ?></span>
						<?php endif; ?>
						<?php if ($details): ?>
							<div class="event-details">
								<p><?php echo $event['event_description']; ?></p>
								<p>
									<?php echo $event['time_string']; ?><br/>
									Contact <a href="mailto:<?php echo $event['leader_email']; ?>"><?php echo $event['leader_name']; ?></a><br/>
									<?php echo $event['leader_phone']; ?>
								</p>
							</div>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</li>
	<?php endforeach; ?>
</ul>

<?php if ($full_page): ?>
		</body>
	</html>
<?php endif; ?>
