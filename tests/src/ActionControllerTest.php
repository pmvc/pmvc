<?php


class ActionControllerTest extends PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $mvc = \PMVC\getC();
        $this->assertTrue(is_a($mvc, '\PMVC\ActionController'));
    }

    public function testProcess()
    {
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index', [
            _CLASS => 'FakeClass',
            ]
        );
        $mvc = $this->getMock('\PMVC\ActionController', ['execute'], [[]]);
        $mvc->expects($this->exactly(2))
            ->method('execute')
            ->will(
                $this->onConsecutiveCalls(
                    (object) [
                    'action' => 'index',
                    ],
                    (object) []
                )
            );
        $mvc($b);
    }

    public function testProcessError()
    {
        $b = new PMVC\MappingBuilder();
        $b->addAction(
            'index',
            [
                _CLASS => 'FakeClass',
                _FORM  => 'FakeFailForm',
            ]
        );
        $b->addForward(
            'error',
            [
                _PATH => 'hello',
                _TYPE => 'view',
            ]
        );
        $options = [
            \PMVC\ERRORS => [
                \PMVC\USER_ERRORS     => 'erros',
                \PMVC\USER_LAST_ERROR => 'last',
            ],
            _RUN_ACTION => 'index',
        ];
        $mvc = PMVC\getC();
        $mvc->setOption($options);
        $view = \PMVC\plug(
            'view', [
            _CLASS => '\PMVC\FakeView',
            ]
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
    public function index($m, $f)
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
