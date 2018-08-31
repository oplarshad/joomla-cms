<?php
/**
 * @version     1.6.0
 * @package     sellacious
 *
 * @copyright   Copyright (C) 2012-2018 Bhartiy Web Technologies. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Izhar Aazmi <info@bhartiy.com> - http://www.bhartiy.com
 */
// no direct access.
defined('_JEXEC') or die;

/** @var  PlgSystemSellaciousImporter  $this */
/** @var  stdClass  $displayData */
$template = $displayData;

JHtml::_('script', 'plg_system_sellaciousimporter/import.images.js', false, true);

$logfile = $this->getState('logfile');
$active  = strlen($logfile) && file_exists($logfile);
?>
<form action="<?php echo JRoute::_('index.php?option=com_importer') ?>"
	  method="post" class="form-horizontal form-import" enctype="multipart/form-data">
	<br/>
	<?php if ($active): ?>

		<div class="txt-color-red"><?php echo JText::_('PLG_SYSTEM_SELLACIOUSIMPORTER_STATE_RUNNING'); ?></div>
		<div class="clearfix"></div>
		<br>

		<div class="pull-left">
			<div class="import-action">
			<button type="button" class="btn btn-sm btn-warning btn-import active"><i class="fa fa-spinner"></i>&nbsp;
				<?php echo JText::_('PLG_SYSTEM_SELLACIOUSIMPORTER_BUTTON_VIEW_STATUS'); ?></button>
			<button type="button" class="btn btn-sm btn-danger"
					onclick="Joomla.submitform('import.cancel', this.form, false);"><i class="fa fa-times"></i>&nbsp;
				<?php echo JText::_('PLG_SYSTEM_SELLACIOUSIMPORTER_BUTTON_CANCEL_IMPORT'); ?></button>
			</div>
		</div>
AC
	<?php else: ?>

		<div class="txt-color-red"><?php echo JText::_('PLG_SYSTEM_SELLACIOUSIMPORTER_STATE_PENDING'); ?></div>
		<div class="clearfix"></div>
		<br>

		<div class="bg-color-white bordered padding-10 col-md-6 col-sm-8 col-xs-12">

			<?php echo $this->renderLayout('section_config', $template, 'default'); ?>
			<div class="clearfix"></div>

		</div>

		<div class="clearfix"></div>
		<br>

		<div class="import-action">
			<button type="button" class="btn btn-sm btn-success btn-import"><i class="fa fa-spinner"></i>&nbsp;
				<?php echo JText::_('PLG_SYSTEM_SELLACIOUSIMPORTER_BUTTON_START_IMPORT'); ?></button>
			<button type="button" class="btn btn-sm btn-danger"
					onclick="Joomla.submitform('import.cancel', this.form, false);"><i class="fa fa-times"></i>&nbsp;
				<?php echo JText::_('PLG_SYSTEM_SELLACIOUSIMPORTER_BUTTON_CANCEL_IMPORT'); ?></button>
		</div>

	<?php endif; ?>

	<div class="clearfix"></div>
	<input type="hidden" name="source" value="<?php echo $template->import_type ?>"/>
	<input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
<div class="clearfix"></div>
<div class="status-viewer hidden">
	<br>
	<div class="import-log"></div>
	<br>
	<button type="button" class="btn btn-sm btn-default btn-import active"><i class="fa fa-spinner"></i>&nbsp;
		<?php echo JText::_('PLG_SYSTEM_SELLACIOUSIMPORTER_BUTTON_VIEW_STATUS'); ?></button>
</div>
