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
        $xmlString  = $this->fetch($this->resource);
        $this->xml  = simplexml_load_string($xmlString);
        if (!$this->xml)
            throw new \Exception('Invalid rss/feed format on ' . $this->resource);

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
                throw new \Exception('Invalid rss/feed format on ' . $this->resource);
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
        if (!empty($this->xml->entry))
        {
            foreach ($this->xml->entry as $entry)
            {
                if (empty($entry->title) || empty($entry->updated) || empty($entry->link->attributes()->href))
                    continue;

                $return[] = array('service'  => 'feed',
                                  'type'     => 'posted',
                                  'resource' => $this->resource,
                                  'stamp'    => (int) strtotime($entry->updated),
                                  'url'      => (string) $entry->link->attributes()->href,
                                  'text'     => (string) $entry->title);
            }
        }

        $this->xml = $this->feedType = null;
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
        if (!empty($this->xml->channel->item))
        {
            foreach ($this->xml->channel->item as $item)
            {
                if (empty($item->pubDate) || empty($item->title) || empty($item->link))
                    continue;

                $return[] = array('service'  => 'feed',
                                  'type'     => 'posted',
                                  'resource' => $this->resource,
                                  'stamp'    => (int) strtotime($item->pubDate),
                                  'url'      => (string) $item->link,
                                  'text'     => (string) $item->title);
            }
        }

        $this->xml = $this->feedType = null;
        return $return;
    }
}
?>
