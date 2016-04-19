<?php

require_once 'sitemap.php';

require_once JPATH_SITE . '/components/com_realestatesearch/models/search.php';

/**
 * Cron job to trash expired cache data
 *
 * @package  Joomla.Cli
 * @since    2.5
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
            $sitemap = '/sitemap-forsale-search.txt';
            $sitemap_url = 'http://www.frenchconnections.co.uk' . $sitemap;
            $handle = fopen(JPATH_SITE . $sitemap, 'w');
            $locs = $this->_getLocations($level = 5);

            $total = count($locs);

            foreach ($locs as $key => $loc)
            {
                $this->out('Processing ' . $key . ' of ' . $total);

                $model = JModelLegacy::getInstance('Search', 'RealestateSearchModel', $config = array('ignore_request' => true));

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
                    $uri->setPath('forsale/' . $loc->alias);
                    fwrite($handle, $uri->toString() . "\n");
                }
            }
            // The URL to ping to let Google know we've updated the sitemap
            $url_to_ping = 'http://google.com/ping?sitemap=' . $sitemap_url;

            $return = $this->myCurl($url_to_ping);

            // Check the return code and send an email if it's not 200 OK
            $this->return_code_check($url_to_ping, $return);
        }
        catch (Exception $e)
        {
            var_dump($e);
        }
    }

}

JApplicationCli::getInstance('GenerateSitemap')->execute();
