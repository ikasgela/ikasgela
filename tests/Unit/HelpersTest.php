<?php

namespace Tests\Unit;

use LengthException;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    // --- mediana() ---

    public function testMedianaOddCount()
    {
        $this->assertEquals(3, mediana([1, 3, 5]));
    }

    public function testMedianaEvenCount()
    {
        $this->assertEquals(2.5, mediana([1, 2, 3, 4]));
    }

    public function testMedianaUnsorted()
    {
        $this->assertEquals(3, mediana([5, 1, 3]));
    }

    public function testMedianaEmptyThrows()
    {
        $this->expectException(LengthException::class);
        mediana([]);
    }

    // --- es_ruta_valida() ---

    public function testEsRutaValidaTinymceFalse()
    {
        $this->assertFalse(es_ruta_valida('es/tinymce/upload'));
    }

    public function testEsRutaValidaLivewireFalse()
    {
        $this->assertFalse(es_ruta_valida('livewire/update'));
    }

    public function testEsRutaValidaNormalTrue()
    {
        $this->assertTrue(es_ruta_valida('cursos/create'));
    }

    // --- truncar_decimales() ---

    public function testTruncarDecimalesCeroDecimales()
    {
        $result = truncar_decimales(5.9, 0);
        $this->assertEquals('5', $result);
    }

    public function testTruncarDecimalesDosDecimales()
    {
        $result = truncar_decimales(3.14159, 2);
        // truncates (rounds down), so 3.14
        $this->assertStringStartsWith('3', $result);
    }

    public function testTruncarDecimalesExportar()
    {
        $result = truncar_decimales(2.555, 2, exportar: true);
        // en_US locale with 2 decimals, truncated
        $this->assertStringContainsString('2', $result);
    }
}
