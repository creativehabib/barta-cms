<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Thin client for any OpenAI-compatible chat-completions endpoint (OpenAI,
 * Azure OpenAI, Groq, local Ollama/LM Studio, etc.). Configured entirely from
 * config/services.php → `ai`, which reads AI_* env vars. When disabled or
 * unconfigured every helper degrades gracefully instead of throwing at the UI.
 */
class AiService
{
    public function enabled(): bool
    {
        return (bool) config('services.ai.enabled', false)
            && filled(config('services.ai.api_key'));
    }

    /** Suggest a few headline options for a piece of copy. */
    public function suggestTitles(string $content, string $locale = 'bn', int $count = 5): array
    {
        $lang = $this->languageName($locale);
        $reply = $this->chat([
            ['role' => 'system', 'content' => "You are a senior news editor writing catchy, accurate {$lang} headlines. Return exactly {$count} options, one per line, no numbering, no quotes."],
            ['role' => 'user', 'content' => "Write {$count} {$lang} headlines for this article:\n\n".$this->trim($content)],
        ]);

        return collect(preg_split('/\r?\n/', trim($reply)))
            ->map(fn ($l) => trim(ltrim($l, "-*0123456789. ")))
            ->filter()
            ->take($count)
            ->values()
            ->all();
    }

    /** A short standalone summary suitable for an excerpt / meta description. */
    public function summarize(string $content, string $locale = 'bn', int $sentences = 2): string
    {
        $lang = $this->languageName($locale);

        return trim($this->chat([
            ['role' => 'system', 'content' => "You summarize news articles in {$lang}. Reply with {$sentences} plain sentences, no preamble."],
            ['role' => 'user', 'content' => $this->trim($content)],
        ]));
    }

    /** Suggest relevant tags/keywords as a flat array. */
    public function suggestTags(string $content, string $locale = 'bn', int $count = 8): array
    {
        $lang = $this->languageName($locale);
        $reply = $this->chat([
            ['role' => 'system', 'content' => "Extract up to {$count} concise {$lang} tags for this article. Reply with a comma-separated list only."],
            ['role' => 'user', 'content' => $this->trim($content)],
        ]);

        return collect(explode(',', $reply))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->take($count)
            ->values()
            ->all();
    }

    /** Translate text between Bengali and English (or any configured locale). */
    public function translate(string $content, string $to = 'en', string $from = 'bn'): string
    {
        $target = $this->languageName($to);
        $source = $this->languageName($from);

        return trim($this->chat([
            ['role' => 'system', 'content' => "You are a professional {$source}→{$target} news translator. Preserve meaning, tone and any HTML tags. Reply with the translation only."],
            ['role' => 'user', 'content' => $content],
        ]));
    }

    /** Rewrite / polish a draft while keeping its meaning. */
    public function improve(string $content, string $locale = 'bn', string $instruction = ''): string
    {
        $lang = $this->languageName($locale);
        $extra = $instruction !== '' ? ' '.$instruction : '';

        return trim($this->chat([
            ['role' => 'system', 'content' => "You are a {$lang} copy editor. Improve clarity, grammar and flow without changing the facts or the language.{$extra} Reply with the edited text only."],
            ['role' => 'user', 'content' => $content],
        ]));
    }

    /**
     * Low-level chat call. Returns the assistant message content.
     *
     * @param  array<int, array{role:string, content:string}>  $messages
     */
    public function chat(array $messages, array $options = []): string
    {
        if (! $this->enabled()) {
            throw new RuntimeException('AI features are disabled. Set AI_ENABLED=true and AI_API_KEY in your .env.');
        }

        $config = config('services.ai');
        $base = rtrim($config['base_url'] ?? 'https://api.openai.com/v1', '/');

        $response = Http::withToken($config['api_key'])
            ->timeout($config['timeout'] ?? 60)
            ->acceptJson()
            ->post($base.'/chat/completions', array_merge([
                'model' => $config['model'] ?? 'gpt-4o-mini',
                'messages' => $messages,
                'temperature' => $config['temperature'] ?? 0.7,
            ], $options));

        if ($response->failed()) {
            throw new RuntimeException('AI request failed: '.$response->status().' '.$response->body());
        }

        return (string) data_get($response->json(), 'choices.0.message.content', '');
    }

    protected function languageName(string $locale): string
    {
        return match ($locale) {
            'bn' => 'Bengali',
            'en' => 'English',
            default => locale_name($locale),
        };
    }

    protected function trim(string $content, int $limit = 6000): string
    {
        $content = trim(strip_tags($content));

        return mb_strlen($content) > $limit ? mb_substr($content, 0, $limit) : $content;
    }
}
