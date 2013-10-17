<?php
/**
 * HtmlList.php
 *
 * @package Formatters
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Formatters;

/**
 * A formatter that acts as a Decorator for the main library.
 * It outputs the lifestream data as a Html list.
 */
class HtmlList extends Template
{
    /** inline {@inheritdoc} */
    protected $template = '<li class="{service}">{date} - {link}</li>';

    /** inline {@inheritdoc} */
    protected $beforeTemplate = '<ul class="simplelifestream">';

    /** inline {@inheritdoc} */
    protected $afterTemplate = '</ul>';
}

?>
