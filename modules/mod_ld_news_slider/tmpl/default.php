<!-- Containing div can be used for styling -->
<div class="<?php echo $params->get( 'moduleclass_sfx' ) ?>" id="" style="margin-bottom:1.5em;">
	<!-- Slideshow containing div positioned relatively - height set directly from paramaters -->
	<div id="featured">
	
		<!-- Another containing div for the slides - used in the js to pick out the slide content -->
		<?php 
			foreach ($this->items as $item) {	
			$image_details = json_decode($item->art_images, true);
		?>
				<img 
					src="<?php echo $image_details['image_intro']; ?>" 
					alt="<?php echo $image_details['image_intro']; ?>" 
					data-caption="#<?php echo $item->id; ?>"
				/>
				<span class="orbit-caption" id="<?php echo $item->id; ?>"><?php echo strip_tags($item->introtext); ?></span>

				
		<?php } ?>	
	</div>
	<!-- Slide show buttons -->
</div>
<script type="text/javascript">
     $(window).load(function() {
         $('#featured').orbit({
			captions : true
		 });
     });
</script>
