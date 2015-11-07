<?php
namespace Helper;

/**
 * here you can define custom actions
 * all public methods declared in helper class will be available in $I
 * @package Helper
 */
class Functional extends \Codeception\Module
{

    /**
     * @param string $command
     * @param bool   $failNonZero
     * @return string
     */
    public function runShellCommandAndGetOutput($command, $failNonZero = true)
    {
        $data = [];
        exec("$command", $data, $resultCode);
        $output = implode("\n", $data);
        if ($output === null) {
            \PHPUnit_Framework_Assert::fail("$command can't be executed");
        }
        if ($resultCode !== 0 && $failNonZero) {
            \PHPUnit_Framework_Assert::fail("Result code was $resultCode.\n\n" . $output);
        }
        $this->debug(preg_replace('~s/\e\[\d+(?>(;\d+)*)m//g~', '', $output));

        return $output;
    }
}
