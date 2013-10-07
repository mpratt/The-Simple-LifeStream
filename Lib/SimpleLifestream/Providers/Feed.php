<?php
/**
 * Feed.php
 *
 * @package Providers
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace SimpleLifestream\Providers;

/**
 * A provider that reads Atom and RSS feeds
 */
class Feed extends Adapter
{
    /** @var object Instance of SimpleXmlElement */
    protected $xml;

    /** inline {@inheritdoc} */
    protected $settings = array(
        'type' => 'posted'
    );

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $this->xml = simplexml_load_string($response);

        if (!$this->xml)
            throw new \Exception('Invalid rss/feed format on ' . $this->getApiUrl());
        else if (!empty($this->xml->entry))
            return $this->atom();
        else if (!empty($this->xml->channel->item))
            return $this->rss();
        else
            return null;
    }

    /** inline {@inheritdoc} */
    public function getApiUrl() { return $this->settings['resource']; }

    /**
     * Converts and organizes data from a Atom feed
     *
     * @return array
     */
    protected function atom()
    {
        $return = array();
        foreach ($this->xml->entry as $entry)
        {
            $return[] = array(
                'type'     => $this->settings['type'],
                'service'  => 'feed',
                'stamp'    => (int) strtotime($entry->updated),
                'url'      => (string) $entry->link->attributes()->href,
                'text'     => (string) $entry->title,
                'resource' => $this->getApiUrl(),
            );
        }

        return $return;
    }

    /**
     * Converts and organizes data from a RSS feed
     *
     * @return array
     */
    protected function rss()
    {
        $return = array();
        foreach ($this->xml->channel->item as $item)
        {
            $return[] = array(
                'service'  => 'feed',
                'type'     => $this->settings['type'],
                'stamp'    => (int) strtotime($item->pubDate),
                'text'     => (string) $item->title,
                'url'      => (string) $item->link,
                'resource' => $this->getApiUrl(),
            );
        }

        return $return;
    }
}
?>
