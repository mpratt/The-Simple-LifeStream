<?php
/**
 * English.php
 *
 * @package Languages
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace SimpleLifestream\Languages;

/**
 * Translation to the english language
 */
class English extends Adapter
{
    /** inline {@inheritdoc} */
    protected $translation = array(
        'link' => '{link}.',
        'posted' => 'posted {link}.',
        'starred' => 'starred {link}.',
        'followed' => 'followed {link}.',
        'commented' => 'commented on {link}.',
        'answered' => 'answered the question: {link}.',
        'accepted' => 'accepted an answer for {link}.',
        'asked' => 'asked {link}.',
        'badge' => 'earned the {link} badge.',
        'repo-released' => 'released version {link}.',
        'repo-created' => 'created the {link} repository.',
        'repo-pushed' => 'pushed a new commit to {link}.',
        'repo-pull-opened' => 'opened a new pull request for {link}.',
        'repo-issue-created' => 'created a new issue on {link}.',
        'tweeted' => 'tweeted: {link}.',
        'favorited' => 'favorited {link}.',
        'bookmarked' => 'bookmarked {link}.',
        'uploaded-video' => 'uploaded the video {link}.',
        'listened' => 'listened to {link}.',
        'created' => 'created {link}.',
        'took-picture' => 'took picture of {link}.',
        'posted-picture' => 'posted picture of {link}.',
        'loved' => 'loved {link}.',
        'ranted' => 'ranted {link}.',
        'liked' => 'liked {link}.',
        'uploaded' => 'uploaded {link}',
    );
}
?>
