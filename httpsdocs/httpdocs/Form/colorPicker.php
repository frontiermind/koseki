<div id="colorPicker" style="position:absolute; width:305px; height:193px; z-index:1; visibility: hidden">
	<table border="0" cellpadding="0" cellspacing="1" bgcolor="#FFFFFF">
		<tr>
			<td colspan="19" style="padding: 2; background-color: #000000; text-align: right"><a href="javascript:closePicker()" style="color: #ffffff; text-decoration: none">[ CLOSE ]</a></td>
		<tr>
<?php 
$bws = array(0xffffff, 0xeeeeee, 0xdddddd, 0xbbbbbb, 0xaaaaaa, 0x888888, 0x777777, 0x555555, 0x444444, 0x222222, 0x111111, 0x000000);
for ($i = 0; $i < 12; $i++) {
?>
		<tr>
<?php
	$g = 0xff - ($i % 6) * 0x33;
	$r_cl = $i < 6 ? 0xff : 0x66;
	$r_fl = $i < 6 ? 0x99 : 0;
	for ($r = $r_cl; $r >= $r_fl; $r -= 0x33) {
		for ($b = 0xff; $b >= 0; $b -= 0x33) {
			$rgb = sprintf("%02x%02x%02x", $r, $g, $b);
			$bw = sprintf("%06x", $bws[$i]);
?>
			<td bgcolor="#<?=$rgb?>"><a href="javascript:putColor('<?=$rgb?>');"><img src="img/space.gif" alt="" height="15" width="15" border="0"></a></td>
<?php
		}
	}
?>
			<td bgcolor="#<?=$bw?>"><a href="javascript:putColor('<?=$bw?>');"><img src="img/space.gif" alt="" height="15" width="15" border="0"></a></td>
		</tr>
<?php
}
?>
	</table>
</div>