<?php
class DefaultFormTest extends PHPUnit_Framework_TestCase
{
    function testDefaultForm()
    {
        $test_str='Hello World!';
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index', array(
            _FUNCTION=>function () use ($test_str) {
                return $test_str;
            },
            _FORM=>'myForm'
            )
        );

        $option = array(
            _DEFAULT_FORM=> 'FakeDefaultForm'
        );
        $mvc = new PMVC\ActionController($option);
        $result = $mvc($b->getMappings());
        $this->assertEquals($test_str, $result[0]);
        $this->assertEquals('aaa', \PMVC\getOption('fakeDefaultForm'));
    }
}

class FakeDefaultForm extends \PMVC\ActionForm
{
    public function validate()
    {
        \PMVC\option('set', 'fakeDefaultForm', 'aaa');
        return true;
    }
}
