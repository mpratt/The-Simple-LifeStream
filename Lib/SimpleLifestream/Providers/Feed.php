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
        'type' => 'posted',
        'service' => 'feed',
        'resource_name' => '',
        'callback' => null,
    );

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        if (empty($this->settings['resource_name'])) {
            $this->settings['resource_name'] = $this->getApiUrl();
        }

        $response = $this->http->fetch($this->getApiUrl());
        $this->xml = simplexml_load_string($response);

        if (!$this->xml) {
            throw new \Exception('Invalid rss/feed format on ' . $this->getApiUrl());
        } else if (!empty($this->xml->entry)) {
            return $this->atom();
        } else if (!empty($this->xml->channel->item)) {
            return $this->rss();
        } else {
            return null;
        }
    }

    /**
     * Converts and organizes data from a Atom feed
     *
     * @return array
     */
    protected function atom()
    {
        $return = array();
        foreach ($this->xml->entry as $entry) {
            $callbackReturn = $this->applyCallbacks($entry);
            $return[] = array_merge($callbackReturn, array(
                'type'     => $this->settings['type'],
                'service'  => $this->settings['service'],
                'stamp'    => (int) strtotime($entry->updated),
                'url'      => (string) $entry->link->attributes()->href,
                'text'     => (string) $entry->title,
                'resource' => $this->settings['resource_name'],
            ));
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
        foreach ($this->xml->channel->item as $item) {
            $callbackReturn = $this->applyCallbacks($item);
            $return[] = array_merge($callbackReturn, array(
                'service'  => $this->settings['service'],
                'type'     => $this->settings['type'],
                'stamp'    => (int) strtotime($item->pubDate),
                'text'     => (string) $item->title,
                'url'      => (string) $item->link,
                'resource' => $this->settings['resource_name'],
            ));
        }

        return $return;
    }
}
?>
