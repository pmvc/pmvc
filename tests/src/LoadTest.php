<?php

namespace PMVC;

class LoadTest extends TestCase
{
    public function testLoadWithLazyFunction()
    {
        Load::plug(
            function () {
                return [
                    [],
                    [],
                    [_VIEW_ENGINE => 'xxx'],
                ];
            }
        );
        $this->assertEquals('xxx', getOption(_VIEW_ENGINE));
        option('set', _VIEW_ENGINE, '');
    }

    public function testLoad()
    {
        Load::plug();
        $this->assertTrue(true);
    }

    public function testRunInSeparateProcess()
    {
        Load::plug(
            [
                'test' => [
                    _PLUGIN_FILE => __DIR__.'/../resources/FakePlugFile.php',
                ],
            ],
            ['./']
        );
        $this->assertTrue(true);
    }

    public function testSetOption()
    {
        option('set', 'foo', 'bar');
        Load::plug([], [], ['foo' => 'bar']);
        $this->assertEquals('bar', getOption('foo'));
    }

    public function testError()
    {
        $Errors = getOption(ERRORS);
        $ref = &ref($Errors->{SYSTEM_ERRORS});
        $ref[] = 'foo1';
        $ref = &ref($Errors->{USER_ERRORS});
        $ref[] = 'foo2';
        $ref = &ref($Errors->{APP_ERRORS});
        $ref[] = 'foo3';
        $expectError = [
            SYSTEM_ERRORS => ['foo1'],
            USER_ERRORS   => ['foo2'],
            APP_ERRORS    => ['foo3'],
        ];
        $this->assertEquals($expectError, get($Errors));
    }
}
