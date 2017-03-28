<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

if (count($this->items) == 1)
{
  echo $this->loadTemplate('single_unit');
}
else
{
  echo $this->loadTemplate('no_units');
}
?>