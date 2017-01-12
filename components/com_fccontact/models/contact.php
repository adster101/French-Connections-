<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

/**
 * HelloWorld Model
 */
class FcContactModelContact extends JModelAdmin
{

  /**
   * @var object item
   */
  protected $item;

  /**
   * Method to get the contact form.
   *
   * The base form is loaded from XML and then an event is fired
   *
   *
   * @param	array	$data		An optional array of data for the form to interrogate.
   * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
   * @return	JForm	A JForm object on success, false on failure
   * @since	1.6
   */
  public function getForm($data = array(), $loadData = false)
  {
    // Get the form.
    $form = $this->loadForm('com_fccontact.contact', 'contact', array('control' => 'jform', 'load_data' => true));
    if (empty($form))
    {
      return false;
    }


    return $form;
  }

  /**
   * Method to get the data that should be injected in the form.
   *
   * @return	mixed	The data for the form.
   * @since	1.6
   */
  protected function loadFormData()
  {
    // Check the session for previously entered form data.
    $data = JFactory::getApplication()->getUserState('com_fccontact.contact.data', array());

    if (empty($data))
    {
      $data = array();
    }

    return $data;
  }

  /**
   *
   * @param JForm $form
   * @param type $data
   * @param type $group
   */
  public function preprocessForm(JForm $form, $data, $group = 'content')
  {
    parent::preprocessForm($form, $data, $group);

    $input = JFactory::getApplication()->input;

    // Flag to determine whether or not to modify for the ask us form
    $askus = $input->get('askus', false, 'boolean');

    // Flag to determine whether or not to modify for the competirion form
    $competition = $input->get('competition', false, 'boolean');

    if ($askus)
    {
      $form->setFieldAttribute('nature', 'default', 'COM_FCCONTACT_NATURE_OF_ENQUIRY_PRE_SALES');
      $form->setFieldAttribute('nature', 'type', 'hidden');
      $form->setFieldAttribute('name', 'labelclass', '');
      $form->setFieldAttribute('email', 'labelclass', '');
      $form->setFieldAttribute('tel', 'labelclass', '');
      $form->setFieldAttribute('message', 'labelclass', '');
      $form->setFieldAttribute('prn', 'required', 'false');
      $form->removeField('captcha');
    }

    if ($competition)
    {
      $form->removeField('captcha');
      $form->setFieldAttribute('message', 'required', 'false');
      $form->setFieldAttribute('nature', 'required', 'false');
    }

  }

  /**
   * Method to auto-populate the model state.
   *
   * This method should only be called once per instantiation and is designed
   * to be called on the first call to the getState() method unless the model
   * configuration flag to ignore the request is set.
   *
   * Note. Calling getState in this method will result in recursion.
   *
   * @return	void
   * @since	1.6
   */
  protected function populateState()
  {

  }

  /**
   *
   * @param type $data
   * @return boolean
   */
  public function save($data)
  {

    $Itemid = SearchHelper::getItemid(array('component', 'com_fccontact'));

    $input = JFactory::getApplication()->input;

    $menu = JMenu::getInstance('site');
    $params = $menu->getParams($Itemid);

    $subject = JText::sprintf('COM_FCCONTACT_EMAIL_SUBJECT', $data['name'], JText::_($data['nature']), $data['prn']);

    $body = JText::sprintf('COM_FCCONTACT_EMAIL_BODY', $data['message'], $data['tel']);

    $from = $data['email'];
    $name = $data['name'];
    $tel = $data['tel'];

    $to = $params->get('contact', 'fchelpdesk@frenchconnections.co.uk');


    // Flag to determine whether or not to modify for the competirion form
    $competition = $input->get('competition', false, 'boolean');
    $competition_name = $input->get('name', false, 'string');
    $optout = $input->get('optout', false, 'boolean');


//var_dump($optout);die;



    if ($competition) {
      /*
       * Save the contact out to db or log file
       */
       JLog::addLogger(
           array(
                // Sets file name
                'text_file' => 'entries.php'
           ),
           // Sets messages of all log levels to be sent to the file
           JLog::INFO,
           // The log category/categories which should be recorded in this file
           // In this case, it's just the one category from our extension, still
           // we need to put it inside an array
           array('com_fccontact')
       );

       JLog::add(JText::sprintf('COM_FCCONTACT_COMPTETITION_LOG', $name, $from, $tel, $competition_name, $optout), JLog::INFO, 'com_fccontact');
    }

    // Don't send any email if this a competition.
    if (!$competition) {

      // Send the registration email. the true argument means it will go as HTML
      $send = JFactory::getMailer()
              ->sendMail($from, $name, $to, $subject, $body, true);

      if (!$send)
      {
        // Log out to file that email wasn't sent for what ever reason;
        // Trigger email to admin / office user. e.g. as per registration.php
        Throw new Exception('Problem sending email for contact form.');
      }

      // Send another email directly to Nick from no-reply@
      if ($data['nature'] == 'COM_FCCONTACT_NATURE_OF_ENQUIRY_PRE_SALES')
      {
        $cc = $params->get('additional_email');

        // Send the registration email. the true argument means it will go as HTML
        $send = JFactory::getMailer()
                ->sendMail('no-reply@frenchconnections.co.uk', $name, $cc, $subject, $body, true);

        if (!$send)
        {
          // Log out to file that email wasn't sent for what ever reason;
          // Trigger email to admin / office user. e.g. as per registration.php
          Throw new Exception('Problem sending email for contact form.');
        }
      }
    }

    // Return the user object we've just created
    return true;
  }

}
