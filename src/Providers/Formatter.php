<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Providers;

use Blaspsoft\Forerunner\Schema\Struct;

interface Formatter
{
    /**
     * Build the provider's structured output payload for the given struct.
     *
     * @return array<string, mixed>
     */
    public function format(Struct $struct): array;
}
