<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
$language = JFactory::getLanguage();
$language->load('com_helloworld', JPATH_ADMINISTRATOR, 'en-GB', true);
?>
<div class="page-header">
	<h1><?php echo $this->item->greeting; ?>&nbsp;
		<small><?php echo JText::_('COM_HELLOWORLD_SITE_HOLIDAY_RENTAL_IN').$this->item->nearest_town.',&nbsp;'.$this->item->title.',' ?><?php echo JText::_('COM_HELLOWORLD_SITE_FRANCE'); ?></small>
	</h1>
</div>
<div class="row">
	<div class="span8">
			
		<?php 
			$searchpath = JPATH_SITE . DS . "images/".$this->item->id;
			if (JFolder::exists($searchpath)) {
				$jpg_files = JFolder::files($searchpath, '.jpg');
			}	
		?>
		<div id="myCarousel" class="carousel">
			<!-- Carousel items -->
			<div class="carousel-inner">
		
				<?php 		
					$counter=0;
					foreach ($jpg_files as $jpg) { 
					$counter = $counter+1;
				?>
					<div class="item<?php if ($counter==1){ echo ' active'; } ?>"><img src="<?php echo 'images/'.$this->item->id.'/'.$jpg ?>" /></div>
				<?php  } ?>
			</div>
			<!-- Carousel nav -->
			<a class="carousel-control left" href="#myCarousel" data-slide="prev">&lsaquo;</a>
			<a class="carousel-control right" href="#myCarousel" data-slide="next">&rsaquo;</a>
		</div>

	</div>
	<div class="span4">
		<div class="row">
			<div class="span2">
				<p><a class="btn btn-small" href="#"><i class="icon-heart"> </i><?php echo JText::_('COM_HELLOWORLD_SITE_ADD_TO_FAVOURITES') ?></a></p>
			</div>
			<div class="span2">
				<!-- AddThis Button BEGIN -->
				<div class="addthis_toolbox addthis_default_style">	
					<a class="addthis_button_preferred_4 addthis_button_print at300b" title="Print" href="#">
						<span class="at16nc at300bs at15nc at15t_print at16t_print">
							<span class="at_a11y">Share on print</span>
						</span>
					</a>
					<a class="addthis_button_preferred_1 addthis_button_facebook at300b" title="Send to Facebook" href="#">
						<span class="at16nc at300bs at15nc at15t_facebook at16t_facebook">
							<span class="at_a11y">Share on facebook</span>
						</span>
					</a>
					<a class="addthis_button_preferred_2 addthis_button_twitter at300b" title="Tweet This" href="#">
						<span class="at16nc at300bs at15nc at15t_twitter at16t_twitter">
							<span class="at_a11y">Share on twitter</span>
						</span>
					</a>
					<a class="addthis_button_preferred_3 addthis_button_email at300b" title="Email" href="#">
						<span class="at16nc at300bs at15nc at15t_email at16t_email">
							<span class="at_a11y">Share on email</span>
						</span>
					</a>
					<a class="addthis_button_compact at300m" href="#">
						<span class="at16nc at300bs at15nc at15t_compact at16t_compact">
							<span class="at_a11y">More Sharing Services</span>
						</span>
					</a>
					<a class="addthis_counter addthis_bubble_style" style="display: block; " href="#">
					<a class="addthis_button_expanded" title="View more services" href="#">4</a>
					<a class="atc_s addthis_button_compact">
					<span></span></a></a>
					<div class="atclear"></div></div>
					<script type="text/javascript" src="http://s7.addthis.com/js/250/addthis_widget.js#pubid=frenchconnections"></script>
					<!-- AddThis Button END -->	
			</div>
		</div>	
		<p><span class="lead"><strong>£560 - £735</strong> </span><?php echo JText::_('COM_HELLOWORLD_SITE_PER_PROPERTY_PER_WEEK') ?></p>
		<p><?php echo JText::_('MOD_FEATURED_PROPERTY_SLEEPS'); echo $this->item->occupancy; ?></p>
		<p><strong><?php echo JText::_('COM_HELLOWORLD_SITE_ACCESS_OPTIONS'); ?></strong>
		<?php foreach ($this->item->params->getValue('access_options') as $access_option) {
			echo JText::_($access_option).'.&nbsp;';
		} ?></p>
	</div>
	<div class="span8">
		<?php echo $this->item->description; ?>
	</div>
	<div class="span4">
		<h3>Where is it</h3>
<iframe width="425" height="350" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="http://maps.google.co.uk/maps?f=q&amp;source=s_q&amp;hl=en&amp;geocode=&amp;q=Finistere,+France&amp;aq=0&amp;oq=finis&amp;sll=53.800651,-4.042969&amp;sspn=22.541458,54.228516&amp;ie=UTF8&amp;hq=&amp;hnear=Finist%C3%A8re,+Brittany,+France&amp;t=m&amp;ll=48.414619,-4.125366&amp;spn=0.638052,1.167297&amp;z=9&amp;iwloc=A&amp;output=embed"></iframe><br /><small><a href="http://maps.google.co.uk/maps?f=q&amp;source=embed&amp;hl=en&amp;geocode=&amp;q=Finistere,+France&amp;aq=0&amp;oq=finis&amp;sll=53.800651,-4.042969&amp;sspn=22.541458,54.228516&amp;ie=UTF8&amp;hq=&amp;hnear=Finist%C3%A8re,+Brittany,+France&amp;t=m&amp;ll=48.414619,-4.125366&amp;spn=0.638052,1.167297&amp;z=9&amp;iwloc=A" style="color:#0000FF;text-align:left">View Larger Map</a></small>
	</div>
</div>



<script>
$(document).ready(function() {
$('.carousel').carousel({
  interval: 2000
})
});
</script>
