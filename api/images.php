<?php
$count = 0;
foreach($images as $img): ?>
	<?php echo ($count % 4 == 0) ? '<ul>' : ''; ?>
		<li><img src="<?php echo $path.'thumbs/'.$img->filename_thumb; ?>" alt="<?php echo $path.$img->filename; ?>" /></li>
	<?php echo (($count+1) % 4 == 0) ? '</ul><div style="clear:both;"></div>' : ''; ?>
<?php 
$count++;
endforeach; ?>