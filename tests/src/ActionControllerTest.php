<?php


class ActionControllerTest extends PHPUnit_Framework_TestCase
{
    function testConstruct()
    {
        $a = array(
            'xxx'=>'yyy'
        );
        $mvc = new PMVC\ActionController($a); 
        $this->assertEquals('yyy', PMVC\getOption('xxx'));
    }

    function testProcess()
    {
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index', array(
            _CLASS=>'FakeClass'
            )
        );
        $mvc = $this->getMock('\PMVC\ActionController', array('execute'), array(array()));
        $mvc->expects($this->exactly(2))
            ->method('execute')
            ->will(
                $this->onConsecutiveCalls(
                    (object)array(
                    'action'=>'index'
                    ),
                    (object)array()
                )
            );
        $mvc($b);
    }

    function testProcessError()
    {
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index',
            array(
                _CLASS=>'FakeClass',
                _FORM=>'FakeFailForm'
            )
        );
        $b->addForward(
            'error', 
            array(
                _PATH=>'hello',
                _TYPE=>'view'
            )
        );
        $options = array(
            \PMVC\ERRORS=>array(
                \PMVC\USER_ERRORS=> 'erros',
                \PMVC\USER_LAST_ERROR=> 'last'
            ),
            _RUN_ACTION=>'index'
        );
        $mvc = new PMVC\ActionController($options); 
        $view = \PMVC\plug(
            'view', array(
            _CLASS=> '\PMVC\FakeView'
            )
        );
        $error = $mvc($b);
        $this->assertEquals(
            $options[\PMVC\ERRORS][\PMVC\USER_ERRORS],
            $error[0]->v['errors']
        );
        $this->assertEquals(
            $options[\PMVC\ERRORS][\PMVC\USER_LAST_ERROR],
            $error[0]->v['lastError']
        );
    }
}

class FakeClass extends PMVC\Action
{
    function index($m, $f) 
    {
        $go = $m->get('home');
        return $go;
    }
}

class FakeFailForm extends PMVC\ActionForm
{
    public function validate()
    {
        return false;
    }
}

