<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class HelloWorldModelHelloWorld extends JModelAdmin {

  /**
   * Method to get the record form.
   *
   * @param	array	$data		Data for the form.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	mixed	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = false) {

    // Get the form.
    $form = $this->loadForm('com_helloworld.snooze', 'snooze', array('control' => 'jform', 'load_data' => $loadData));
    if (empty($form)) {
      return false;
    }

    return $form;
  }
  
}