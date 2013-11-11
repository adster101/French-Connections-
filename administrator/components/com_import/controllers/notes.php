<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');
jimport('joomla.user.user');
jimport('joomla.user.helper');

/**
 * HelloWorld Controller
 */
class ImportControllerNotes extends JControllerForm {

  public function import() {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    // The file we are importing from
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');

    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    // Get a db instance
    $db = JFactory::getDBO();

    while (($line = fgetcsv($handle, $length = '', $delimiter = '|')) !== FALSE) {


      $admin_notes = explode('<!--!>', $line[1]);

      foreach ($admin_notes as $key => $note) {

        $pat = '/\[(.*?)\]/'; // text between [] - ie the date note added

        preg_match($pat, $note, $matches);

        if (!empty($matches)) { // $matches[0] is string with the outer quotes
          $body = str_replace($matches[0], '', $note);

          if (strlen($body) > 6) {

            // Start building a new query to insert any attributes...        
            $query = $db->getQuery(true);

            $query->insert('#__property_notes');

            $query->columns(array('property_id', 'subject', 'body', 'created_time'));

            $insert_string = '';

            $fields = array();

            $insert_string = $line[0] . ',' . $db->quote('') . ',' . $db->quote($body) . ',' . $db->quote($matches[1]);

            $query->values($insert_string);

            // Set and execute the query
            $db->setQuery($query);

            if (!$db->execute()) {
              $e = new JException(JText::sprintf('JLIB_DATABASE_ERROR_STORE_FAILED_UPDATE_ASSET_ID', $db->getErrorMsg()));
              print_r($db->getErrorMsg());
              print_r($insert_string);
            }
          }
        }
      }
    }

    fclose($handle);
    $this->setMessage('Notes imported, hooray!');
    $this->setRedirect('index.php?option=com_import&view=notes');
  }

}
