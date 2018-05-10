<?php

/**
 * @package    Joomla.Cli
 *
 * @copyright  Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// Initialize Joomla framework
        const _JEXEC = 1;

// Load system defines
if (file_exists(dirname(__DIR__) . '/defines.php')) {
    require_once dirname(__DIR__) . '/defines.php';
}

if (!defined('_JDEFINES')) {
    define('JPATH_BASE', dirname(__DIR__));
    require_once JPATH_BASE . '/includes/defines.php';
}

// Get the framework.
require_once JPATH_LIBRARIES . '/import.legacy.php';

// Bootstrap the CMS libraries.
require_once JPATH_LIBRARIES . '/cms.php';

// Import the configuration.
require_once JPATH_CONFIGURATION . '/configuration.php';

// Import our base real estate cli bit
jimport('frenchconnections.cli.realestateimport');

class CoteMerCotejardinImmobilier extends RealestateImport {

    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute() {
        // Get a db instance and start a transaction
        $db = JFactory::getDbo();
        $user = JFactory::getUser('contact@cotemercotejardinimmobilier.com')->id;

        $this->out('About to get feed...');

        // Get and parse out the feed
        $props = $this->parseFeed('http://www.cotemercotejardinimmobilier.com/cotemercotejardinimmobilier.xml', 'annonces');

        $this->out('Got feed...');

        // Add realestate and rental tables to the include path
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_realestate/tables');

        // Add the realestate property models
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_realestate/models');
        $model = JModelLegacy::getInstance('Image', 'RealEstateModel');
        define('COM_IMAGE_BASE', JPATH_ROOT . '/images/property/');

        // Add the classification table so we can get the location details
        JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_classification/tables');

        $this->out('About to process feed results...');

        jimport('joomla.filesystem.folder');

        // Loop over each of the $props returned from parseFeed above
        foreach ($props->properties as $prop) {
            try {
                $db->transactionStart();

                $this->out('Processing... ' . $prop->agency_reference);

                // Check whether this property agency reference already exists in the versions table
                $property_version = $this->getPropertyVersion('#__realestate_property_versions', 'agency_reference', $prop->agency_reference, $db);

                $id = ($property_version->realestate_property_id) ? $property_version->realestate_property_id : '';

                $this->out('Version ID: ' . $id . ' for ' . $prop->agency_reference);

                // Get the location details for this property
                // TO DO - This should be using the $prop lat and long using the getNearestCity base method
                $classification = JTable::getInstance('Classification', 'ClassificationTable');
                $location = $classification->getPath($prop->city);

                if (!$id) {

                    $this->out('Adding property entry...');

                    // Create an entry in the #__realestate_property table
                    $property_id = $this->createProperty($db, $user);



                    if (empty($location)) {
                        throw new Exception('Incorrect location detail for ' . $prop->agency_reference . 'cotemercotejardin');
                    }

                    $data = array();
                    $data['realestate_property_id'] = $property_id;
                    $data['agency_reference'] = $db->quote($prop->agency_reference);
                    $data['title'] = $db->quote($prop->title);
                    $data['country'] = (int) $location[1]->id;
                    $data['area'] = (int) $location[2]->id;
                    $data['region'] = (int) $location[3]->id;
                    $data['department'] = (int) $location[4]->id;
                    $data['city'] = (int) $location[5]->id;
                    $data['latitude'] = $prop->latitude;
                    $data['longitude'] = $prop->longitude;
                    $data['created_by'] = $user; // TO DO get Allez Francais added to system - surpress renewal reminders
                    $data['created_on'] = $db->quote(JFactory::getDate());
                    $data['description'] = $db->quote($prop->description, true);
                    $data['bedrooms'] = (int) $prop->bedrooms;
                    $data['bathrooms'] = (int) $prop->bathrooms;
                    $data['base_currency'] = $db->quote('EUR');
                    $data['price'] = (int) $prop->price;
                    $data['review'] = 0;
                    $data['published_on'] = $db->quote(JFactory::getDate());

                    $this->out('Adding property version...');

                    JTable::getInstance('PropertyVersions', 'RealestateTable');
                    $property_version_id = $this->createPropertyVersion($db, $data);

                    $this->out('Working through images...');

                    foreach ($prop->images as $i => $image) {

                        $image_parts = explode('/', $image);

                        // Get the last two parts and implode it to make the name
                        $image_name = implode('-', array_slice($image_parts, -2, 2));

                        // The ultimate file path where we want to store the image
                        $filepath = JPATH_SITE . '/images/property/' . $property_id . '/' . $image_name;

                        // Check the property directory exists...
                        if (!file_exists(JPATH_SITE . '/images/property/' . $property_id)) {
                            JFolder::create(JPATH_SITE . '/images/property/' . $property_id);
                        }

                        if (!file_exists($filepath)) {
                            // Copy the image url directly to where we want it
                            copy($image, $filepath);

                            // Generate the profiles
                            $model->generateImageProfile($filepath, (int) $property_id, $image_name, 'gallery', 578, 435);
                            $model->generateImageProfile($filepath, (int) $property_id, $image_name, 'thumbs', 100, 100);
                            $model->generateImageProfile($filepath, (int) $property_id, $image_name, 'thumb', 210, 120);
                        }

                        // Save the image data out to the database...
                        $this->createImage($db, array($property_version_id, $property_id, $db->quote($image_name), $i + 1));
                    }
                } else {

                    $this->out('Updating expiry date...');

                    // Update the expiry date
                    $this->updateProperty($db, $id);

                    $this->out('Updating version details...');

                    // Update the property version in case price or description has changed...
                    $data = array();
                    $data['id'] = $id;
                    $data['agency_reference'] = $db->quote($prop->agency_reference);
                    $data['title'] = $db->quote($prop->title);
                    $data['description'] = $db->quote($prop->description, true);
                    $data['bedrooms'] = (int) $prop->bedrooms;
                    $data['latitude'] = $prop->latitude;
                    $data['longitude'] = $prop->longitude;
                    $data['bathrooms'] = (int) $prop->bathrooms;
                    $data['base_currency'] = $db->quote('EUR');
                    $data['price'] = (int) $prop->price;
                    $data['review'] = 0;
                    $data['published_on'] = $db->quote(JFactory::getDate());
                    $data['city'] = (int) $location[5]->id;

                    $this->updatePropertyVersion($db, $data);

                    // Yep, we have it already!
                    // Update expiry date
                    // Update version to shut down any unpublished versions? Need to deal with this somehow?
                    // $ref = $this->updateProperty($realestate_property_version->id);
                }

                // Done so commit all the inserts and what have you...
                $db->transactionCommit();

                $this->out('Done processing... ' . $prop->agency_reference);
            } catch (Exception $e) {
                // Roll back any batched inserts etc
                $db->transactionRollback();

                // Send an email, woot!
                $this->email($e);

            }
        }
    }

}

JApplicationCli::getInstance('CoteMerCotejardinImmobilier')->execute();
