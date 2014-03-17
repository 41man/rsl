<div class="image-library-header clearfix">
	<div class="image-library-title">
		<?php if(isset($tags)): ?>
			<?php echo form_open() ?>
			<input type="text" id="tag-search" />
			<input type="hidden" id="tag-search-id" name="tag_id" />
			<input type="submit" id="tag-search-submit" value="Search by Tag" />
			<?php echo form_close() ?>
		<?php else: ?>
			<h4><?php echo $title ?></h4>
		<?php endif ?>
	</div>
	
	<?php if(isset($industries)): ?>
	<div class="image-library-industry-dropdown">
		<?php echo form_open(); ?>
		<select id="industry-dropdown">
			<option value="">Browse by industry</option>
			<?php foreach($industries as $ind): ?>
			<option value="<?php echo $ind->id; ?>"><?php echo $ind->name; ?> (<?php echo $ind->ct; ?>)</option>
			<?php endforeach; ?>
		</select>
		<?php echo form_close(); ?>
	</div>
	<?php endif ?>
</div>

<div class="image-library-images">
<?php echo $this->load->view('ajax/image_library/images') ?>
</div>

<script type="text/javascript">
var availableTags = <?php echo !empty($tags) ? json_encode($tags) : '{}' ?>;
</script>