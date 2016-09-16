<?php

function parseEPL($file) { 
	$file = file_get_contents($file);
	$file = explode("\n", $file); 
	foreach($file as $row) {
		$row = explode(',', $row);
		if($row[0] !== 'b13')
			if(!@is_numeric($row[1]) || !@is_numeric($row[2]) || !@is_numeric(trim($row[3]))) {
				continue;
			}
		#print_r($row);
		$left = preg_replace('/[^0-9]/', '', $row[0]);
		if(!is_numeric($left)) continue;
		$top = $row[1];
		#print $row[0].'<br />';
		$data = '';
		$font = 0;
		$background = false;
		$box = false;
		$bold = false;
		$rot = 0;
		
		$width = $row[2];
		$height = $row[3];
		
		switch(substr($row[0], 0, 2)) {
			case 'LO':
				$background = 'black';
				break;
			case 'GW':
				$box = true;
				$width *= 8;
				$background = 'black';
				break;
		}
		switch(substr($row[0], 0, 1)) {
			case 'b':
				if($row[2] == 'M') {
					$background = 'gray';
					$width = 210;
					$height = 210;
				}
				
			break;
			case 'B':
				$background = 'gray';
				
				$width = 10;
				switch($row[3].'-'.$row[4]) {
					case '1-3': $w = $row[6] * 2.8; break;
				}
				$width = $w;
				$height = $row[6];
				
				break;
			case 'A':
				if(@$row[8]) {
					$txt = array_splice($row, 7);
					$txt = implode(', ', $txt);
				} else {
					$txt = $row[7];
				}
				$data = str_replace(' ', '&nbsp;', trim(trim($txt), '"'));
				#print $row[3].'='.$data.'<br />';
				$bold = false;
				$scaler = 1;
				switch($row[3]) {
					case '1': $font = 10; break;
					case '2': $font = 20; break;
					case '3': $font = 23; break;
					case '4': $scaler = 0.93; $font = 28; $bold = true; break;
					case '5': $font = 58; break;
					default: $font = 96; break;
				}
				
				switch($row[2]) {
					case '0': break;
					case '3': $rot = -90; break;
				}
				break;
		}
		echo '<div style="
			'.($font?'-webkit-transform-origin: 0 0; -webkit-transform:rotate('.$rot.'deg) scale('.($scaler*$row[4]).','.$row[5].');':'').'
			line-height: '.($font*0.8).'px; font-family: monospace; 
			font-size: '.$font.'px;
			'.($bold?'font-weight: bold;':'').'
			'.($box?'border: 1px solid black; ':'').'
			position: absolute;
			'.($background?'background: '.$background.';':'').'
			left: '.$left.'px; 
			top: '.$top.'px;
			'.($width?'width: '.trim($width).'px; ':'').'
			height: '.trim($height).'px;
		">'.$data.'</div>';
	}
}
?>
<div style="width: 1800px;">
	<div style="position: relative; width: 900px; height: 5px; float: left;">
	<? parseEPL('Shipment-Label-1-15928.epl'); ?>
	</div>
	<div style="position: relative; width: 900px; height: 5px; float: left;">
	<? parseEPL('Shipment-Label-1-51937.epl'); ?>
	</div>
</div>
