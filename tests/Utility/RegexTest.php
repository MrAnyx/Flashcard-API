<?php

namespace App\Tests\Utility;

use App\Utility\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testInteger(): void
    {
        $this->assertEquals("\d+", Regex::INTEGER);
    }

    public function testUsername(): void
    {
        $this->assertEquals("^[\w\-\.]*$", Regex::USERNAME);
    }

    public function testUsernameSlash(): void
    {
        $this->assertEquals("/^[\w\-\.]*$/", Regex::USERNAME_SLASH);
    }
}
