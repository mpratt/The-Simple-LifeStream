<?php
/**
 * Feed.php
 * A service that reads Atom and RSS feeds
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Services;

class Feed extends \SimpleLifestream\Core\Adapter
{
    protected $xml;
    protected $feedType;

    /**
     * Gets the data of the url and returns an array
     * with all the information.
     *
     * @return array
     */
    public function getApiData()
    {
        if (!function_exists('simplexml_load_string'))
            throw new Exception('The Feed service requires the simplexml_load_string function.');

        $xmlString  = $this->http->fetch($this->resource);
        $this->xml  = simplexml_load_string($xmlString);

        if ($this->xml === false)
            return array();

        $this->feedType = strtolower($this->xml->getName());

        switch ($this->feedType)
        {
            case 'feed':
                return $this->atomFeed();
                break;

            case 'rss':
                return $this->rssFeed();
                break;

            default:
                return array();
                break;
        }
    }

    /**
     * Detects Atom feeds and organizes the data properly.
     *
     * @return array
     */
    protected function atomFeed()
    {
        $return = array();
        if ($this->xml !== false && !empty($this->xml->entry))
        {
            foreach ($this->xml->entry as $entry)
            {
                if (empty($entry->title) || empty($entry->updated) || empty($entry->link->attributes()->href))
                    continue;

                $return[] = array('service'  => 'feed',
                                  'type'     => 'posted',
                                  'resource' => parse_url($this->resource, PHP_URL_HOST),
                                  'stamp'    => (int) strtotime($entry->updated),
                                  'url'      => (string) $entry->link->attributes()->href,
                                  'text'     => (string) $entry->title);
            }
        }

        return $return;
    }

    /**
     * Detects RSS feeds and organizes the data properly.
     *
     * @return array
     */
    protected function rssFeed()
    {
        $return = array();
        if ($this->xml !== false && !empty($this->xml->channel->item))
        {
            foreach ($this->xml->channel->item as $item)
            {
                if (empty($item->pubDate) || empty($item->title) || empty($item->link))
                    continue;

                $return[] = array('service'  => 'feed',
                                  'type'     => 'posted',
                                  'resource' => parse_url($this->resource, PHP_URL_HOST),
                                  'stamp'    => (int) strtotime($item->pubDate),
                                  'url'      => (string) $item->link,
                                  'text'     => (string) $item->title);
            }
        }

        return $return;
    }
}
?>
