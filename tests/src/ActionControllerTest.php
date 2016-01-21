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
        $b->addForward(
            'home', array(
            _PATH=>'hello'
            ,_TYPE=>'view'
            ,_LAZY_OUTPUT=>'xxx'
            )
        );
        $mvc = $this->getMock('\PMVC\ActionController', array('execute'), array(array()));
        $mvc->setMapping($b->getMappings());
        $mvc->expects($this->exactly(2))
            ->method('execute')
            ->will(
                $this->returnValue(
                    (object)array(
                    'lazyOutput'=>true
                    )
                )
            );
        $mvc->process();
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
            )
        );
        $mvc = new PMVC\ActionController($options); 
        $mvc->setMapping($b->getMappings());
        $view = \PMVC\plug(
            'view', array(
            _CLASS=> '\PMVC\FakeView'
            )
        );
        $error = $mvc->execute('index');
        $this->assertEquals(
            $options[\PMVC\ERRORS][\PMVC\USER_ERRORS],
            $error->v['errors']
        );
        $this->assertEquals(
            $options[\PMVC\ERRORS][\PMVC\USER_LAST_ERROR],
            $error->v['lastError']
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

