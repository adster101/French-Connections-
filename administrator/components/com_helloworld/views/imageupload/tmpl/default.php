<?php
// No direct access

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');

$id = JRequest::getVar('id', '0', 'GET', 'integer');
$parent_id = JRequest::getVar('parent_id', '1', 'GET', 'integer');
?>


