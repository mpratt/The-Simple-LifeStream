<?php
/**
 * DailyMotion.php
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
 * A provider for dailymotion.com
 */
class DailyMotion extends Feed
{
    /** inline {@inheritdoc} */
    public function __construct(array $settings)
    {
        $settings = array(
            'type' => 'uploaded-video',
            'service' => 'dailymotion',
            'resource' => 'http://www.dailymotion.com/rss/user/' . $settings['resource'],
            'resource_name' => $settings['resource']
        );

        parent::__construct($settings);
    }
}
?>
