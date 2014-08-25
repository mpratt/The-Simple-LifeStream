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
    protected $url = '';

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $xml = simplexml_load_string($response);

        if (!empty($response))
            return array_map(array($this, 'filterResponse'), $response);

        if (!$xml)
            throw new \Exception('Invalid xml format on ' . $this->getApiUrl());

        return array();
    }

    /** inline {@inheritdoc} */
    protected function filterResponse(array $value = array())
    {
        /*
        return array(
            'service'  => 'pinboard',
            'type'     => 'bookmarked',
            'resource' => $value['a'],
            'stamp'    => strtotime($value['dt']),
            'url'      => $value['u'],
            'text'     => $value['d'],
        );
        */
    }

}
?>
