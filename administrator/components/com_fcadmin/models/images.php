<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelform');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

class FcadminModelImages extends JModelForm
{

  /**
   * Method to get the menu item form.
   *
   * @param   array      $data        Data for the form.
   * @param   boolean    $loadData    True if the form is to load its own data (default case), false if not.
   * @return  JForm    A JForm object on success, false on failure
   * @since   1.6
   */
  public function getForm($data = array(), $loadData = true)
  {
    // Get the form.
    $form = $this->loadForm('com_fcadmin.images', 'images', array('control' => 'jform', 'load_data' => $loadData));
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
    $data = JFactory::getApplication()->input->get('jform', array(), 'array');

 
    return $data;
  }
  /**
   * Build imagelist
   *
   * @param string $listFolder The image directory to display
   * @since 1.5
   */
  public function getList()
  {

    // Get current path from request
    $basePath = $this->getState('folder');

    // If undefined, set to empty
    if ($basePath == 'undefined')
    {
      $basePath = '';
    }

    $mediaBase = str_replace(DIRECTORY_SEPARATOR, '/', COM_MEDIA_BASE . '/');

    $images = array();
    $folders = array();
    $docs = array();

    $fileList = false;
    $folderList = false;
    if (file_exists($basePath))
    {
      // Get the list of files and folders from the given folder
      $fileList = JFolder::files($basePath);
      $folderList = JFolder::folders($basePath);
    }

    // Iterate over the files if they exist
    if ($fileList !== false)
    {
      foreach ($fileList as $file)
      {
        if (is_file($basePath . '/' . $file) && substr($file, 0, 1) != '.' && strtolower($file) !== 'index.html')
        {
          $tmp = new JObject;
          $tmp->name = $file;
          $tmp->title = $file;
          $tmp->path = str_replace(DIRECTORY_SEPARATOR, '/', JPath::clean($basePath . '/' . $file));
          $tmp->path_relative = str_replace($mediaBase, '', $tmp->path);
          $tmp->size = filesize($tmp->path);

          $ext = strtolower(JFile::getExt($file));
          switch ($ext)
          {
            // Image
            case 'jpg':
            case 'png':
            case 'gif':
            case 'xcf':
            case 'odg':
            case 'bmp':
            case 'jpeg':
            case 'ico':
              $info = @getimagesize($tmp->path);
              $tmp->width = @$info[0];
              $tmp->height = @$info[1];
              $tmp->type = @$info[2];
              $tmp->mime = @$info['mime'];


              $tmp->width = $tmp->width;
              $tmp->height = $tmp->height;


              $images[] = $tmp;
              break;

            // Non-image document
            default:
              $tmp->icon_32 = "media/mime-icon-32/" . $ext . ".png";
              $tmp->icon_16 = "media/mime-icon-16/" . $ext . ".png";
              $docs[] = $tmp;
              break;
          }
        }
      }
    }





    return $images;
  }

}
