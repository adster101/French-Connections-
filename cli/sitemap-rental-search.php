<?php

// Include the sitemap class
require_once 'sitemap.php';

// Include the search model class
require_once JPATH_SITE . '/components/com_fcsearch/models/search.php';

/**
 * Cron job to generate a list of search urls for use in a sitemap
 *
 */
class GenerateSitemap extends Sitemap
{

    /**
     * Entry point for the script
     *
     * @return  void
     *
     * @since   2.5
     */
    public function doExecute()
    {
        try
        {
            $sitemap = '/sitemap-rental-search.txt';
            $sitemap_url = 'http://www.frenchconnections.co.uk' . $sitemap;
            $handle = fopen(JPATH_SITE . $sitemap, 'w');

            $locs = $this->_getLocations($level = 5);

            $total = count($locs);

            foreach ($locs as $key => $loc)
            {
                $this->out('Processing ' . $key . ' of ' . $total);

                $model = JModelLegacy::getInstance('Search', 'FcSearchModel', $config = array('ignore_request' => true));


                // Set the listing ID we are sending the reminder to
                $model->setState('list.searchterm', $loc->alias);
                $model->setState('search.location', $loc->id);
                $model->setState('search.level', $loc->level);
                $model->setState('search.latitude', $loc->latitude);
                $model->setState('search.longitude', $loc->longitude);
                // Don't limit the search results returned so we get them all
                $model->setState('list.limit', 0);

                $results = $model->getResults();

                $uri = new JUri();
                $uri->setHost('http://www.frenchconnections.co.uk/');


                if (count($results) > 0)
                {
                    $uri->setPath('accommodation/' . $loc->alias);
                    fwrite($handle, $uri->toString() . "\n");
                }

                $property_types = $model->getRefinePropertyOptions();

                foreach ($property_types as $property_type)
                {
                    $uri->setPath('accommodation/' . $loc->alias . '/' . 'property_' . JApplication::stringURLSafe($property_type->title) . '_' . (int) $property_type->id);
                    fwrite($handle, $uri->toString() . "\n");
                }

                $accommodation_types = $model->getRefineAccommodationOptions();

                foreach ($accommodation_types as $accommodation_type)
                {
                    $uri->setPath('accommodation/' . $loc->alias . '/' . 'accommodation_' . JApplication::stringURLSafe($accommodation_type->title) . '_' . (int) $property_type->id);

                    fwrite($handle, $uri->toString() . "\n");
                }
            }

            // TO DO - Either split file out into four or add a sitemap index file
            // and ping the index file.
        }
        catch (Exception $e)
        {
            var_dump($e);
        }
    }

}

JApplicationCli::getInstance('GenerateSitemap')->execute();
