<?php
namespace Ajgon\LintPackBundle\Command;

use Ajgon\LintPackBundle\Test\LintPackTestCase;

class LintCsslintCommandTest extends LintPackTestCase
{
    public function setUp()
    {
        $this->command = new LintCsslintCommand();
        parent::setUp();
    }

    public function testCsslintIfCommandHasGoodName()
    {
        $this->assertEquals('lint:csslint', $this->command->getName());
    }

    public function testCsslintConfigurationWithEmptyBin()
    {
        $this->assertEmptyConfigParameter('csslint', 'bin', false);
    }

    public function testCsslintConfigurationWithEmptyLocations()
    {
        $this->assertEmptyConfigParameter('csslint', 'locations', true);
    }

    public function testCsslintIfDoesntLaunchWhenDisabled()
    {
        $this->assertDisabledConfig('csslint');
    }

    public function testCsslintEmptyConfiguration()
    {
        $config = $this->getEmptyTestConfig();

        $this->initWithConfig($config);
        $this->assertEquals(
            $this->getProperCommand($config),
            $this->command->getCommand()
        );
    }

    public function testCsslintIfProperCommandIsBuilt()
    {
        $this->assertEquals(
            $this->getProperCommand($this->getTestConfig()),
            $this->command->getCommand()
        );
    }

    public function testCsslintIfProperCommandIsBuiltForDefaults()
    {
        $this->command = new LintCsslintCommand();
        $this->initWithoutConfig();

        $this->assertEquals(
            $this->getProperCommand($this->getDefaultConfig()),
            $this->command->getCommand()
        );
    }

    public function testCsslintIfProcessExecutesCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['csslint']['bin'] = 'true';

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(0, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\nDone, without errors.\n\n", $output->fetch());
    }

    public function testCsslintIfProcessFailsCorrectly()
    {
        $config = $this->getTestConfig();
        $config['lint_pack']['csslint']['bin'] = 'false';

        list($returnValue, $output) = $this->executeClassWithConfig($config);

        $this->assertEquals(1, $returnValue);
        $this->assertEquals($this->getProperCommand($config) . "\nCommand failed.\n\n", $output->fetch());
    }

    private function getProperCommand($config)
    {
        $config['lint_pack']['csslint']['locations'] =
            $this->parseConfigDirs($config['lint_pack']['csslint']['locations']);

        return $config['lint_pack']['csslint']['bin'] .
               (
                   $config['lint_pack']['csslint']['disable_rules'] ?
                   ' --ignore=' . implode(',', $config['lint_pack']['csslint']['disable_rules']) :
                   ''
               ) .
               (
                   $config['lint_pack']['csslint']['ignores'] ?
                   ' --exclude-list=' . implode(',', $config['lint_pack']['csslint']['ignores']) :
                   ''
               ) .
               ' ' . implode(',', $config['lint_pack']['csslint']['locations']);
    }
}
