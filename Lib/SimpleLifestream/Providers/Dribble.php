<?php
/**
 * Dribble.php
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
 * A provider for Dribble
 */
class Dribble extends Adapter
{
    /** inline {@inheritdoc} */
    protected $url = 'http://api.dribbble.com/players/%s/shots';

    /** inline {@inheritdoc} */
    public function getApiData()
    {
        $response = $this->http->fetch($this->getApiUrl());
        $response = json_decode($response, true);

        if (!empty($response['shots']))
            return array_map(array($this, 'filterResponse'), $response['shots']);

        return null;
    }

    /** inline {@inheritdoc} */
    protected function filterResponse(array $value = array())
    {
        return array(
            'service'  => 'dribble',
            'type'     => 'posted',
            'resource' => $value['player']['name'],
            'stamp'    => strtotime($value['created_at']),
            'url'      => $value['url'],
            'text'     => $value['title'],
            'avatar'   => $value['player']['avatar_url'],
            'thumbnail' => $value['image_teaser_url'],
        );
    }
}
?>
