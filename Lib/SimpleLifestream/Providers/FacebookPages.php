<?php
/**
 * FacebookPages.php
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
 * A provider for Facebook Pages
 */
class FacebookPages extends Feed
{
    /** inline {@inheritdoc} */
    protected $settings = array(
        'content_length' => '80',
        'content_delimiter' => '...',
    );

    /** inline {@inheritdoc} */
    public function __construct(array $settings)
    {
        $settings = array_merge($settings, array(
            'type' => 'link',
            'service' => 'facebookpages',
            'resource' => 'http://www.facebook.com/feeds/page.php?format=rss20&id=' . $settings['resource'],
            'resource_name' => $settings['resource']
        ));

        parent::__construct($settings);
    }

    /** inline {@inheritdoc} */
    protected function rss()
    {
        $return = array();
        foreach ($this->xml->channel->item as $item) {
            $callbackReturn = $this->applyCallbacks($item);
            $return[] = array_merge($callbackReturn, array(
                'service'  => $this->settings['service'],
                'type'     => $this->settings['type'],
                'stamp'    => (int) strtotime($item->pubDate),
                'text'     => (string) $this->getStatusText($item),
                'url'      => (string) $item->link,
                'resource' => (string) $item->author,
            ));
        }

        return $return;
    }

    /**
     * Retrieves text from either item title or content
     *
     * @param SimpleXMLElement $item
     * @return string
     */
    protected function getStatusText(\SimpleXMLElement $item)
    {
        $text = $item->link;
        if (trim($item->title) !== '')
            $text = $item->title;
        else if (trim(strip_tags($item->description)) !== '')
            $text = strip_tags($item->description);

        return $this->truncate(trim($text));
    }

    /**
     * Truncates the $text
     *
     * @param string $text
     * @return string
     */
    protected function truncate($text)
    {
        if (strlen($text) > $this->settings['content_length']) {
            return substr($text, 0, $this->settings['content_length']) . $this->settings['content_delimiter'];
        }

        return $text;
    }
}
?>
