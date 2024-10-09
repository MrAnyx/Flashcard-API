<?php

declare(strict_types=1);

namespace App\Tests\Utility;

use App\Utility\Regex;
use PHPUnit\Framework\TestCase;

class RegexTest extends TestCase
{
    public function testInteger(): void
    {
        $this->assertEquals('\\d+', Regex::INTEGER);
    }

    public function testUsername(): void
    {
        $this->assertEquals('^[\\w\\-\\.]*$', Regex::USERNAME);
    }

    public function testUsernameSlash(): void
    {
        $this->assertEquals('/^[\\w\\-\\.]*$/', Regex::USERNAME_SLASH);
    }

    public function testPassword(): void
    {
        $this->assertEquals('^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\\d]){1,})(?=(.*[\\W]){1,})(?!.*\\s).{8,}$', Regex::PASSWORD);
    }

    public function testPasswordSlash(): void
    {
        $this->assertEquals('/^(?=.*?[A-Z])(?=(.*[a-z]){1,})(?=(.*[\\d]){1,})(?=(.*[\\W]){1,})(?!.*\\s).{8,}$/', Regex::PASSWORD_SLASH);
    }
}
