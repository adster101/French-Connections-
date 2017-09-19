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
class ImportControllerArticles extends JControllerForm
{

  public function import()
  {

    // Check that this is a valid call from a logged in user.
    JSession::checkToken('POST') or die('Invalid Token');

    $config = JFactory::getConfig();
    // This is here as the user table instance checks that we aren't trying to insert a record with the same 
    // username as a super user. However, by default root_user is null. As we insert a load of dummy user to start 
    // with this is matched and the user thinks we are trying to replicate the root_user. We aren't and we 
    // explicity say there here by setting root_user in config.
    $config->set('root_user', 'admin');
    $userfile = JRequest::getVar('import_file', null, 'files', 'array');

    // Add the content model
    JControllerForm::addModelPath(JPATH_ADMINISTRATOR . '/components/com_content/models');

    // Open a handle to the import file
    $handle = fopen($userfile['tmp_name'], "r");

    $model = $this->getModel('Article', 'ContentModel');

    $db = JFactory::getDbo();

    while (($line = fgetcsv($handle, 0, $delimiter = '|')) !== FALSE)
    {
      $data = array();
      $output = '';


      $output .= iconv("ISO-8859-1", "UTF-8//TRANSLIT", strip_tags($line[4], "<p>"));
      $output .= iconv("ISO-8859-1", "UTF-8//TRANSLIT", strip_tags($line[5], "<p>"));

      if (!empty($line[10]))
      {

        $query = $db->getQuery(true);
        $query->select('fde_filename')
                ->from('qitz3_file_details')
                ->where('fde_id in (' . $line[10] . ')');

        $db->setQuery($query);
        $row = $db->loadObjectList();

        $output .= '<h4>Linked resources</h4>';


        foreach ($row as $file)
        {
          $link = "media/fc/assets/pdf/" . $file->fde_filename;

          $output .= '<p><a href="' . $link . '">' . $file->fde_filename . "</a></p>";
        }
      }


      if (!empty($line[11]))
      {
        $query = $db->getQuery(true);
        $query->select('fde_filename')
                ->from('qitz3_file_details')
                ->where('fde_id = ' . $line[11]);
        $db->setQuery($query);

        $row = $db->loadObject();

        $data['images'] = array();
        $data['images']['float_fulltext'] = '';
        $data['images']['float_intro'] = '';
        $data['images']['image_fulltext'] = 'images/general/press/' . $row->fde_filename;
        $data['images']['image_fulltext_alt'] = '';
        $data['images']['image_fulltext_caption'] = '';
        $data['images']['image_intro'] = '';
        $data['images']['image_intro_alt'] = '';
        $data['images']['image_intro_caption'] = '';
      }

      $data['introtext'] = '';
      $data['fulltext'] = $output;
      $data['id'] = '';
      $data['state'] = ($line[1] == 'True') ? 1 : 0;
      $data['title'] = iconv("ISO-8859-1", "UTF-8//TRANSLIT", $line[3]);
      $data['created'] = $line[2];
      $data['catid'] = 38;
      $data['language'] = 'en-GB';
      $data['metadesc'] = '';
      $data['metakey'] = '';

      //$data['publish_up'] = date('Y-m-d', strtotime($line[14]));
      //$data['publish_down'] = date('Y-m-d', strtotime($line[15]));
      $model = $this->getModel('Article', 'ContentModel');

      if (!$model->save($data))
      {
        $error = $model->getError();
        
      }
    }


    fclose($handle);

    $this->setMessage('Articles imported, hooray!');

    $this->setRedirect('index.php?option=com_import&view=articles');
  }

}
