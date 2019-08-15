<?php namespace Acrolinx\SDK;

use Acrolinx\SDK\Utils\BatchCheckIdGenerator;
use PHPUnit\Framework\TestCase;

class UtilsTest extends TestCase
{
    /**
     * Test generate Batch Check UUID
     */
    public function testGetBatchCheckUUID()
    {
        $uuid = BatchCheckIdGenerator::getId('test');
        $this::assertContains('gen.test.', $uuid);
    }

    /**
     * Test generate Batch Check UUID with no initial value
     */
    public function testGetBatchCheckUUIDWithSpace()
    {
        $uuid = BatchCheckIdGenerator::getId(' ');
        $this::assertContains('gen.phpSDK.', $uuid);
    }

    /**
     * Test generate Batch Check UUID with no initial value
     */
    public function testGetBatchCheckUUIDWithNoInitialValue()
    {
        $uuid = BatchCheckIdGenerator::getId('');
        $this::assertContains('gen.phpSDK.', $uuid);
    }

    /**
     * Test generate Batch Check UUID with no initial value
     */
    public function testGetBatchCheckUUIDWithWhiteSpace()
    {
        $uuid = BatchCheckIdGenerator::getId('Acrolinx for PHP');
        $this::assertContains('gen.Acrolinx-for-PHP.', $uuid);
    }
}
