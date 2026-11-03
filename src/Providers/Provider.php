<?php

declare(strict_types=1);

namespace Blaspsoft\Forerunner\Providers;

use Blaspsoft\Forerunner\Schema\Struct;

enum Provider: string
{
    case OpenAI = 'openai';
    case Anthropic = 'anthropic';
    case Gemini = 'gemini';

    /**
     * Get the formatter that builds this provider's structured output payload.
     */
    public function formatter(): Formatter
    {
        return match ($this) {
            self::OpenAI => new OpenAIFormatter,
            self::Anthropic => new AnthropicFormatter,
            self::Gemini => new GeminiFormatter,
        };
    }

    /**
     * Format a struct as this provider's structured output payload.
     *
     * @return array<string, mixed>
     */
    public function format(Struct $struct): array
    {
        return $this->formatter()->format($struct);
    }
}
