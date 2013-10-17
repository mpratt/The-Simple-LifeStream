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
 * It outputs the lifestream data as a string - Html list.
 */
class HtmlList
{
    /** @var object Instance of \SimpleLifestream\Formatters\Template */
    protected $lifestream;

    /**
     * Constructor
     *
     * @param object $lifestream Instance of \SimpleLifestream\SimpleLifestream
     * @return void
     */
    public function __construct(\SimpleLifestream\SimpleLifestream $lifestream)
    {
        $this->lifestream = new \SimpleLifestream\Formatters\Template($lifestream);
    }

    /**
     * Gets the lifestream data and returns
     * a string with the template
     *
     * @param int $count
     * @return string
     */
    public function getLifestream($count = 0)
    {
        $this->lifestream->beforeTemplate('<ul class="simplelifestream">');
        $this->lifestream->setTemplate('<li class="{service}">{date} - {link}</li>');
        $this->lifestream->afterTemplate('</ul>');

        return $this->lifestream->getLifestream($count);
    }

    /**
     * Truly decorate the lifestream object
     *
     * @param string $method
     * @param array  $args
     * @return mixed
     *
     * @throws InvalidArgumentException when a method was not found
     */
    public function __call($method, $args)
    {
        return call_user_func_array(array($this->lifestream, $method), $args);
    }
}

?>
