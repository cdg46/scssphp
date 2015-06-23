<?php
/**
 * SCSSPHP
 *
 * @copyright 2012-2015 Leaf Corcoran
 *
 * @license http://opensource.org/licenses/gpl-license GPL-3.0
 * @license http://opensource.org/licenses/MIT MIT
 *
 * @link http://leafo.net/scssphp
 */

namespace Leafo\ScssPhp\Tests;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\LineCommentator;

function _dump($value)
{
    fwrite(STDOUT, print_r($value, true));
}

function _quote($str)
{
    return preg_quote($str, '/');
}

/**
 * Input test - runs all the tests in inputs/ and compares their output to ouputs/
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
class InputTest extends \PHPUnit_Framework_TestCase
{
    protected static $inputDir = 'inputs';
    protected static $outputDir = 'outputs';
    protected static $outputNumberedDir = 'outputs_numbered';

    public function setUp()
    {
        $this->scss = new Compiler();
        $this->scss->addImportPath(__DIR__ . '/' . self::$inputDir);
    }

    /**
     * @dataProvider fileNameProvider
     */
    public function testInputFile($inFname, $outFname)
    {
        if (getenv('BUILD')) {
            return $this->buildInput($inFname, $outFname);
        }

        if (!is_readable($outFname)) {
            $this->fail("$outFname is missing, consider building tests with BUILD=1");
        }

        $input = file_get_contents($inFname);
        $output = file_get_contents($outFname);

        $this->assertEquals($output, $this->scss->compile($input));
    }

    /**
     * Run all tests with line numbering
     *
     * @dataProvider numberedFileNameProvider
     */
    public function testLineNumbering($inFname, $outFname)
    {
        //insert line numbers
        $scss = LineCommentator::insertLineComments(file($inFname), basename($inFname));

        if (getenv('BUILD')) {
            //write css
            $css = $this->scss->compile($scss);

            file_put_contents($outFname, $css);
        }

        if (!is_readable($outFname)) {
            $this->fail("$outFname is missing, consider building tests with BUILD=true");
        }

        $output = file_get_contents($outFname);

        $this->assertEquals($output, $this->scss->compile($scss));
    }

    public function fileNameProvider()
    {
        return array_map(
            function ($a) {
                return array($a, InputTest::outputNameFor($a));
            },
            self::findInputNames()
        );
    }

    public function numberedFileNameProvider()
    {
        return array_map(
            function ($a) {
                return array($a, InputTest::outputNumberedNameFor($a));
            },
            self::findInputNames()
        );
    }

    // only run when env is set
    public function buildInput($inFname, $outFname)
    {
        $css = $this->scss->compile(file_get_contents($inFname));
        file_put_contents($outFname, $css);
    }

    public static function findInputNames($pattern = '*')
    {
        $files = glob(__DIR__ . '/' . self::$inputDir . '/' . $pattern);
        $files = array_filter($files, 'is_file');

        if ($pattern = getenv('MATCH')) {
            $files = array_filter($files, function ($fname) use ($pattern) {
                return preg_match("/$pattern/", $fname);
            });
        }

        return $files;
    }

    public static function outputNameFor($input)
    {
        $front = _quote(__DIR__ . '/');
        $out = preg_replace("/^$front/", '', $input);

        $in = _quote(self::$inputDir . '/');
        $out = preg_replace("/$in/", self::$outputDir . '/', $out);
        $out = preg_replace('/.scss$/' , '.css', $out);

        return __DIR__ . '/' . $out;
    }

    public static function outputNumberedNameFor($input)
    {
        $front = _quote(__DIR__ . '/');
        $out = preg_replace("/^$front/", '', $input);

        $in = _quote(self::$inputDir . '/');
        $out = preg_replace("/$in/", self::$outputNumberedDir . '/', $out);
        $out = preg_replace('/.scss$/' , '.css', $out);

        return __DIR__ . '/' . $out;
    }

    public static function buildTests($pattern)
    {
        $files = self::findInputNames($pattern);

        foreach ($files as $file) {
        }
    }
}
