<?php
/**
 * TestServiceFacebookPages.php
 *
 * @author  Michael Pratt <pratt@hablarmierda.net>
 * @link    http://www.michael-pratt.com/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TestServiceFacebookPages extends TestService
{
    /** inline {@inheritdoc} */
    protected $validTypes = array('link');

    /**
     * This needs more execution time ..
     * @large
     */
    public function testFacebookPagesRequest()
    {
        $stream = $this->getStream('FacebookPages', '27469195051');
        $response = $stream->getResponse();

        $this->checkResponseIntegrity('FacebookPages', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testFacebookPagesRequestFail()
    {
        $stream = $this->getStream('FacebookPages', 'an-invalid-id-here');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testSample1()
    {
        $stream = $this->getStream('FacebookPages', 'PHP', 'php-03-02-2015.rss');
        $response = $stream->getResponse();

        $this->assertEquals(13, count($response));
        $this->checkResponseIntegrity('FacebookPages', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testTruncate()
    {
        $data = array(
            'resource' => 'PHP',
            'content_length' => 1,
            'content_delimiter' => '-',
        );

        $stream = $this->getStream('FacebookPages', $data, 'php-03-02-2015.rss');
        $response = $stream->getResponse();

        foreach ($response as $r) {
            $this->assertTrue(isset($r['text']));
            $this->assertTrue(strlen($r['text']) === 2); // length + delimiter
            $this->assertTrue((bool) preg_match('~-$~', $r['text']));
        }
    }

    public function testSample2()
    {
        $stream = $this->getStream('FacebookPages', 'Facebook', 'facebook-03-02-2015.rss');
        $response = $stream->getResponse();

        $this->assertEquals(6, count($response));
        $this->checkResponseIntegrity('FacebookPages', $response);

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }

    public function testServiceInvalidAnswer()
    {
        $stream = $this->getStream('FacebookPages', 'dummyInvalidResourceNotXML', 'not a xml response');
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testServiceInvalidAnswer2()
    {
        $invalidResponse = '
                <?xml version="1.0" encoding="utf-8"?>
                <rss version="2.0"
                      xmlns:media="http://search.yahoo.com/mrss/"
                      xmlns:dc="http://purl.org/dc/elements/1.1/"
                    >
                  <channel>
                    <title>Coca-Cola&apos;s Facebook Wall</title>
                    <link>https://www.facebook.com/</link>
                    <description>Coca-Cola&apos;s Facebook Wall</description>
                    <language>en-us</language>
                    <category domain="Facebook">PageSyndicationFeed</category>
                    <generator>Facebook Syndication</generator><docs>http://www.rssboard.org/rss-specification</docs>
                    <webMaster>webmaster@facebook.com</webMaster>
                  </channel>
                  <access:restriction relationship="deny" xmlns:access="http://www.bloglines.com/about/specs/fac-1.0" />
              </rss>';

        $stream = $this->getStream('FacebookPages', 'dummyInvalidResourceNotValidRSS', $invalidResponse);
        $stream->getResponse();

        $errors = $stream->getErrors();
        $this->assertTrue(!empty($errors));
    }

    public function testCallback()
    {
        $stream = $this->getStream('FacebookPages', 'Facebook', 'facebook-03-02-2015.rss');
        $stream->addCallback(function ($v) {
            return array(
                'modified_title' => 'custom-title-' . str_replace(' ', '', $v['title'])
            );
        });

        $response = $stream->getResponse();
        $this->checkResponseIntegrity('FacebookPages', $response, array('modified_title'));
        $this->assertTrue((strpos($response['0']['modified_title'], ' ') === false));
        $this->assertEquals(6, count($response));

        $errors = $stream->getErrors();
        $this->assertTrue(empty($errors));
    }
}
?>
