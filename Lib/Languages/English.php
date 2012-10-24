<?php
/**
 * English.php
 * Translation to the english language
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Languages;

class English extends \SimpleLifestream\Core\Lang
{
    protected $translation = array('pushEvent'   => 'pushed a new commit to {link}.',
                                   'createEvent' => 'created the {link} repository.',
                                   'createGist'  => 'created a new Gist {link}',
                                   'updateGist'  => 'updated a Gist {link}',
                                   'starred'     => 'starred {link}.',
                                   'favorited'   => 'favorited {link}.',
                                   'followed'    => 'followed {link}.',
                                   'commented'   => 'commented on "{link}".',
                                   'posted'      => 'posted {link}.',
                                   'tweeted'     => 'tweeted "{link}".',
                                   'link'        => '{link}.',
                                   'answered'    => 'answered the "{link}" question.',
                                   'asked'       => 'asked "{link}".',
                                   'acceptedAnswer' => 'accepted an answer for "{link}".',
                                   'badgeWon'    => 'won the {link} badge.'
                               );
}
?>
