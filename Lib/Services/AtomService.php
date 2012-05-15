<?php
/**
 * AtomService.php
 * A service that reads Atom and RSS feeds
 *
 * @author    Michael Pratt <pratt@hablarmierda.net>
 * @link http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

class AtomService extends SimpleLifestreamAdapter
{
    // Instead of a username you need to specify a url
    protected $requires = array('url');
    protected $translation = array('en' => array('publish' => 'wrote new article <a href="%s">%s</a>.'),
                                   'es' => array('publish' => 'publicó la entrada <a href="%s">%s</a>.'));

    /**
     * Gets the data of the url and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        $xmlString = $this->fetchUrl($this->config['url']);
        $xml    = simplexml_load_string($xmlString);
        $type   = strtolower($xml->getName());
        $return = array();

        switch ($type)
        {
            // Its an atom Feed
            case 'feed':
                if (!empty($xml->entry))
                {
                    foreach ($xml->entry as $entry)
                    {
                        if (empty($entry->title) || empty($entry->updated) || empty($entry->link->attributes()->href))
                            continue;

                        $return[] = array('service' => 'atom',
                                          'date' => strtotime($entry->updated),
                                          'html' => $this->translate('publish', $entry->link->attributes()->href, $entry->title));
                    }
                }
            break;

            // Its a RSS feed
            case 'rss':
                if (!empty($xml->channel->item))
                {
                    foreach ($xml->channel->item as $item)
                    {
                        if (empty($item->pubDate) || empty($item->title) || empty($item->link))
                            continue;

                        $return[] = array('service' => 'atom',
                                          'date' => strtotime($item->pubDate),
                                          'html' => $this->translate('publish', $item->link, $item->title));
                    }
                }
            break;
        }

        return $return;
    }
}
?>
