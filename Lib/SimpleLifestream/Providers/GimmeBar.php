<?php
/**
 * GimmeBar.php
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
 * A provider for GimmeBar
 */
class GimmeBar extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'https://gimmebar.com/api/v1/public/assets/%s.json';

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response['records']))
            return array_map(array($this, 'filterResponse'), $response['records']);

        return null;
    }

    /** inline {@inheritdoc} */
    protected function filterResponse(array $value = array())
    {
        return array(
            'service'  => 'gimmebar',
            'type'     => 'bookmarked',
            'resource' => $this->settings['resource'],
            'stamp'    => $value['date'],
            'url'      => 'http://gim.ie/' . $value['short_url_token'],
            'text'     => $value['title'],
        );
    }
}
?>
