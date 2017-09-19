<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

// no direct access
defined('_JEXEC') or die;

jimport ( 'joomla.plugin.plugin');

class plgContentGenerateProfileImages extends JPlugin {
  
  public function onContentAfterSave( $event, $args ) {
    print_r($event);die;
  }
}

?>
