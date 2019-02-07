<?php
namespace Root4root\Reshaper\Test;

use PHPUnit\Framework\TestCase;
use Root4root\Reshaper\Configurator;
use Root4root\Reshaper\Reshaper;

/**
 * Since Reshaper class is so easy, let's test whole library.
 */

class ReshaperTest extends TestCase
{
     /**
     * @dataProvider provideFullData
     */
    public function testReshaper($config, $input, $expected) 
    {
        $config = new Configurator($config['fields'], $config['reqired']);
        $reshaper = new Reshaper($config);

        $this->assertEquals($expected, $reshaper->parseRow($input)->getResult());
    }

    public function provideFullData()
    {
        return [
            [
                ['fields' => ['(A)i','(B)s', '(B+C)s'],
                 'reqired' => ['(A)i', '(G|H|I)r(/^(?:\s*)[1-9,\+]+(?:\s*)$/)']],
                [],
                []
            ],
            [
                ['fields' => ['(A)i','(B)s', '(B+C)s'],
                 'reqired' => ['(A)i', '(G|H|I)r(/^(?:\s*)[1-9,\+]+(?:\s*)$/)']],
                [1, 'PARTNUMBER', 'Part.333', 'Description', 'foo1', 'foo2', '+', 0, 0],
                [1, 'PARTNUMBER', 'PARTNUMBER Part.333']
            ],
            [
                ['fields' => ['(A)i','(B)f', '(B+C)s'],
                 'reqired' => ['(A)i']],
                [1, 'PARTNUMBER', 'Part.333', 'Description', 'foo1', 'foo2', '+', 0, 0],
                [1, 0.0, 'PARTNUMBER Part.333']
            ],
            [
                ['fields' => ['(A)i','(B)f', '(B+C)s'],
                 'reqired' => ['(A)i', '(B)f']],
                [1, 'PARTNUMBER', 'Part.333', 'Description', 'foo1', 'foo2', '+', 0, 0],
                []
            ],
            [
                ['fields' => ['(C+G+E)s'],
                 'reqired' => ['(G|H|I)r(/^(?:\s*)[1-9,\+]+(?:\s*)$/)']],
                [1, 'PARTNUMBER', 'Part.333', 'Description', 'foo1', 'foo2', '+', 0, 0],
                ['Part.333 + foo1']
            ],
        ];
    }
}
