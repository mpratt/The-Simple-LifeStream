<?php
/**
 * Pinboard.php
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
 * A provider for Pinboard
 */
class Pinboard extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'https://api.pinboard.in/v1/posts/recent?auth_token=%s';

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $xml = simplexml_load_string($response);

        if (!$xml)
            throw new \Exception('Invalid xml format on ' . $this->getApiUrl());
        else
            return $this->filterXMLArray($xml);
    }

    /**
     * Coverts XML output from Pinboard API into return array
     *
     * @return array
     */
    protected function filterXMLArray($xml)
    {
        $return = array();
        foreach ($xml as $post)
        {
            $return[] = array(
                'service'  => 'pinboard',
                'type'     => 'bookmarked',
                'resource' => (string) $post['hash'],
                'stamp'    => strtotime($post['time']),
                'url'      => (string) $post['href'],
                'text'     => (string) $post['d'],
            );
        }

        return $return;
    }

}
?>
