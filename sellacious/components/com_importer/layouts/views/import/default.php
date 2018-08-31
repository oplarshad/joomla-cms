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

/** @var  $this  ImporterViewImport  */
JHtml::_('bootstrap.tooltip');
JHtml::_('jquery.framework');
JHtml::_('behavior.keepalive');

JHtml::_('stylesheet', 'com_importer/view.import.css', array('version' => S_VERSION_CORE, 'relative' => true));
JHtml::_('script', 'com_sellacious/util.tabstate.js', array('version' => S_VERSION_CORE, 'relative' => true));
JHtml::_('script', 'com_importer/view.import.js', array('version' => S_VERSION_CORE, 'relative' => true));
JHtml::_('script', 'com_sellacious/plugin/serialize-object/jquery.serialize-object.min.js', array('version' => S_VERSION_CORE, 'relative' => true));

$dispatcher = JEventDispatcher::getInstance();
$app        = JFactory::getApplication();
$active     = $app->getUserState('com_importer.import.state.handler', key($this->handlers));

echo JHtml::_('bootstrap.startTabSet', 'import', array('active' => $active));

foreach ($this->handlers as $key => $handler)
{
	ob_start();
	$dispatcher->trigger('onImportRenderLayout', array('com_importer.import', $handler->name));
	$html = ob_get_clean();

	if (trim($html) != '')
	{
		echo JHtml::_('bootstrap.addTab', 'import', $handler->name, $handler->title);
		echo $html;
		echo JHtml::_('bootstrap.endTab');
	}
}

echo JHtml::_('bootstrap.endTabSet');
