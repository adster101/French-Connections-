<?php
/**
 * @package		Joomla.Site
 * @subpackage	Templates.beez5
 * @copyright	Copyright (C) 2005 - 2009 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
$app = JFactory::getApplication();
$templateparams =$app->getTemplate(true)->params;
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers');
$cparams = JComponentHelper::getParams('com_media');

// Get any module content published to the accommodation-side-bar module position
$doc      = JFactory::getDocument();
$renderer = $doc->loadRenderer( 'modules' );
$raw      = array( 'style' => 'xhtml' );
$sidebarContent = $renderer->render('accommodation-side-bar', $raw, null);

// If the page class is defined, add to class as suffix.
// It will be a separate class if the user starts it with a space
?>
<section class="blog<?php echo $this->pageclass_sfx;?>">
<?php if ($this->params->get('show_page_heading')!=0 or $this->params->get('show_category_title')): ?>
<h1>
	<?php echo $this->escape($this->params->get('page_heading')); ?>
	<?php if ($this->params->get('show_category_title'))
	{

		echo '<span class="subheading-category">'.$this->category->title.'</span>';
	}
	?>
</h1>
<?php endif; ?>

<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
	<div class="category-desc">
	<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
		<img src="<?php echo $this->category->getParams()->get('image'); ?>"/>
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->category->description) : ?>
		<?php echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
	<?php endif; ?>
	</div>
<?php endif; ?>
</section>
<div class="row">

<?php if ($sidebarContent) { ?>
	<div class="span6">
		<div class="row">
<?php } ?>

<?php $leadingcount=0 ; ?>
<?php if (!empty($this->lead_items)) : ?>
	<?php foreach ($this->lead_items as &$item) : ?>
	<div class="span3">
		<article class="leading-<?php echo $leadingcount; ?><?php echo $item->state == 0 ? 'system-unpublished' : null; ?>">
			<?php
				$this->item = &$item;
				echo $this->loadTemplate('item');
			?>
		</article>
		<?php
			$leadingcount++;
		?>
	</div>
	<?php endforeach; ?>
	<?php if ($sidebarContent) { ?>
	</div>

<?php } ?>
<?php endif; ?>
<?php
	$introcount=(count($this->intro_items));
	$counter=0;
?>
<?php if (!empty($this->intro_items)) : ?>

	<?php foreach ($this->intro_items as $key => &$item) : ?>
	<?php
		$key= ($key-$leadingcount)+1;
		$rowcount=( ((int)$key-1) %	(int) $this->columns) +1;
		$row = $counter / $this->columns ;

		if ($rowcount==1) : ?>
	<div class="items-row cols-<?php echo (int) $this->columns;?> <?php echo 'row-'.$row ; ?>">
	<?php endif; ?>
	<article class="item column-<?php echo $rowcount;?><?php echo $item->state == 0 ? ' system-unpublished' : null; ?>">
		<?php
			$this->item = &$item;
			echo $this->loadTemplate('item_intro');
		?>
	</article>
	<?php $counter++; ?>
	<?php if (($rowcount == $this->columns) or ($counter ==$introcount)): ?>
				<span class="row-separator"></span>
				</div>

			<?php endif; ?>
	<?php endforeach; ?>


<?php endif; ?>

<?php if (!empty($this->link_items)) : ?>

	<?php echo $this->loadTemplate('links'); ?>

<?php endif; ?>


	<?php if (is_array($this->children[$this->category->id]) && count($this->children[$this->category->id]) > 0 && $this->params->get('maxLevel') !=0) : ?>
		<div class="cat-children">
		<h3>
<?php echo JTEXT::_('JGLOBAL_SUBCATEGORIES'); ?>
</h3>
			<?php echo $this->loadTemplate('children'); ?>
		</div>
	<?php endif; ?>

<?php if (($this->params->def('show_pagination', 1) == 1  || ($this->params->get('show_pagination') == 2)) && ($this->pagination->get('pages.total') > 1)) : ?>
		<div class="pagination">
						<?php  if ($this->params->def('show_pagination_results', 1)) : ?>
						<p class="counter">
								<?php echo $this->pagination->getPagesCounter(); ?>
						</p>

				<?php endif; ?>
				<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
<?php  endif; ?>


<?php if ($sidebarContent) { ?>
	</div>


<div class="span4">
<?php echo $sidebarContent; ?>
</div>


<?php } ?>
</div>

