<?php
	foreach($dealers as $dealer) {
		$number = substr($dealer->getNumber(), -3);
		echo '<option value="'.$dealer->getId().'">'.sprintf("%s - %s", $number, $dealer->getName()).'</option>';
	}
?>