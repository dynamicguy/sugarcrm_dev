<?php

class JSIterator implements Iterator {
    private $data = array();
    private $key = 0;
    private $test_files = array();

    public function __construct($directory, $expectFiles = TRUE) {
        $root_dir = realpath('tests/jssource/'.$directory);
        $test_dir = $root_dir.'/test/';
        if($expectFiles) {
            $expect_dir = $root_dir.'/expect/';
        }

        $test_contents = scandir($test_dir);
        foreach($test_contents as $possible_file) {
            $test_file = $test_dir.$possible_file;
            if($expectFiles) {
                $expect_file = $expect_dir.$possible_file;
                if(is_file($test_file) && is_file($expect_file)) {
                    $this->test_files[] = array($test_file, $expect_file);
                }
            } else {
                if(is_file($test_file)) {
                    $this->test_files[] = array($test_file);
                }
            }
        }
    }

    public function current() {
        $ret = array();
        foreach($this->test_files[$this->key] as $file) {
            $ret[] = file_get_contents($file);
        }
        return $ret;
    }

    public function key() {
        return $this->key;
    }

    public function rewind() {
        reset($this->test_files);
        $this->data = array();
        $this->key = 0;
    }

    public function valid() {
        return $this->key < count($this->test_files);
    }

    public function next() {
        ++$this->key;
    }
}

class SugarJSMinTest extends PHPUnit_Framework_TestCase {
    /**
     * @dataProvider minifyProvider
     */
    public function testMinify($unminified, $minified) {
        require_once('jssource/jsmin.php');
        $this->assertEquals(SugarMin::minify($unminified), $minified);
    }

    public function minifyProvider() {
        return new JSIterator('minify');
    }
}
