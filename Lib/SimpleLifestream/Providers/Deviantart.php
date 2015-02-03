<?php
/**
 * Deviantart.php
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
 * A provider for deviantart.com
 */
class Deviantart extends Feed
{
    /** inline {@inheritdoc} */
    public function __construct(array $settings)
    {
        $settings = array_merge($settings, array(
            'service' => 'deviantart',
            'resource' => 'http://backend.deviantart.com/rss.xml?q=gallery%3A' . urlencode($settings['resource']) . '&type=deviation',
            'resource_name' => $settings['resource']
        ));

        parent::__construct($settings);
    }
}
?>
