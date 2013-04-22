<?php
/**
 * @version     1.0.0
 * @package     com_itemcosts
 * @copyright   Copyright (C) 2013. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Adam Rifat <adam@littledonkey.net> - http://
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Itemcost controller class.
 */
class ItemcostsControllerItemcost extends JControllerForm
{

    function __construct() {
        $this->view_list = 'itemcosts';
        parent::__construct();
    }

}