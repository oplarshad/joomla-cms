<?php
/**
 * @version     1.6.0
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     SPL Sellacious Private License; see http://www.sellacious.com/spl.html
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// no direct access
defined('_JEXEC') or die;

/** @var array $displayData */
$field   = (object) $displayData;
$images  = $field->value ? (array) $field->value : array();
?>
<!-- row -->
<div class="row" id="<?php echo $field->id ?>_wrapper">
	<input type="file" id="<?php echo $field->id ?>" name="<?php echo $field->name ?>" class="hidden" />
	<!-- SuperBox -->
	<div class="superbox col-sm-12"><?php
		foreach ($images as $image)
		{
			?><div class="superbox-list bg-color-white">
				<img src="<?php echo JUri::root(true).'/'.$image->path ?>" data-img="<?php echo JUri::root(true).'/'.$image->path ?>" data-id="<?php echo $image->id ?>" class="superbox-img">
			</div><?php
		}
		?><div class="superbox-list-add bg-color-white">
			<?php echo JHtml::_('image', 'com_sellacious/add-icon.png', null, ' class="jffci-add" style="padding: 20%;" ', true); ?>
		</div><!--
		--><div class="superbox-float"></div>
	</div>
	<!-- SuperBox -->
	<div class="superbox-show bg-color-white" style="height:300px; display: none"></div>
</div>
<!-- end row -->
