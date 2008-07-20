<?php if (count($contact['Contract']) > 0): ?>

	<?php
		$today = date('Y-m-d');
		$now = date('H:i');
		
		$hasUnpaidContract = false;
		foreach($contact['Contract'] as $contract) 
		{
			if($contract['paid_on'] == '' || $contract['paid_on'] > $today)
			{
				$hasUnpaidContract = true;
			}
		}
		
		if($hasUnpaidContract) 
		{
			$class = 'unpaidContractIndicator';
		} else {
			$class = 'paidContractIndicator';
		}
	?>
	<div class="<?php echo $class ?>">&nbsp;</div>

<?php elseif (count($contact['Meeting']) > 0): ?>
	
	<?php
		$today = date('Y-m-d');
		$now = date('H:i');
		
		$hasFutureMeeting = false;
		foreach($contact['Meeting'] as $meeting) 
		{
			$meetingTime = $meeting['time'];
			if(strlen($meetingTime) == 4) $meetingTime = "0" . $meetingTime;
			
			if($meeting['date'] > $today || ($meeting['date'] == $today && $meetingTime > $now))
			{
				$hasFutureMeeting = true;
			}
		}
		
		if($hasFutureMeeting) 
		{
			$class = 'futureMeetingIndicator';
		} else {
			$class = 'meetingIndicator';
		}
	?>
	<div class="<?php echo $class ?>">&nbsp;</div>
<?php else: ?>
	&nbsp;
<?php endif ?>