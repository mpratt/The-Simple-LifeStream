<?php
/**
 * Adapter.php
 *
 * @package SimpleLifestream
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Languages;

/**
 * Abstract Class responsable of helping with translations.
 * Every translation should extend this class.
 *
 * @abstract
 */
abstract class Adapter
{
    /** @var array Associative array with translations */
    protected $translation;

    /**
     * Returns the string related to the key. Params are determined by
     * the func_get_args() function but the format of this method looks like this:
     *
     *     ->get('key');
     *     ->get('key', 'value1');
     *     ->get('key', 'value1', 'value2');
     *
     * @param string $key
     * @param string ...$value
     * @return string
     *
     * @throws InvalidArgumentException When the no arguments were found
     */
    public function get()
    {
        if (func_num_args() < 1) {
            throw new \InvalidArgumentException('The get method expects at least a key value');
        }

        $args = func_get_args();
        $key = strtolower($args['0']);
        array_shift($args);

        if (!empty($this->translation[$key])) {
            return vsprintf($this->translation[$key], (array) $args);
        }

        return '{resource}';
    }

    public function getRelativeTranslation($key, $num)
    {
        if ($key == 'just_now') {
            return $this->get('time_' . $key);
        } else if ($num == 1) {
            return $this->get('time_' . $key . '_singular', $num);
        } else {
            return $this->get('time_' . $key, abs($num));
        }
    }
}
?>
