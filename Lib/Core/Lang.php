<?php
/**
 * Lang.php
 * Every translation should extend this class.
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Core;

abstract class Lang
{
    protected $translation;

    /**
     * Returns the string related to the $key
     *
     * @param string $key
     * @return string
     */
    public function get($key)
    {
        if (!empty($this->translation[$key]))
            return $this->translation[$key];

        return '{resource}';
    }
}
?>
