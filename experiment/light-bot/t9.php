<?php

class SmartLiteBot
{
    private array $index = [];
    private array $sentences = [];

    /**
     * Обучение на большом тексте
     */
    public function train(string $text): void
    {
        // Разбиваем текст на предложения
        $this->sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($this->sentences as $id => $sentence) {
            $words = $this->tokenize($sentence);
            // Создаем инвертированный индекс для быстрого поиска
            foreach (array_unique($words) as $word) {
                if (mb_strlen($word) > 2) { // Игнорируем предлоги
                    $this->index[$word][] = $id;
                }
            }
        }
    }

    /**
     * Генерация ответа на основе запроса
     */
    public function ask(string $query): string
    {
        $queryWords = $this->tokenize($query);
        $relevantSentenceIds = [];

        // 1. Ищем ID предложений, где встречаются слова из запроса
        foreach ($queryWords as $word) {
            if (isset($this->index[$word])) {
                foreach ($this->index[$word] as $id) {
                    $relevantSentenceIds[$id] = ($relevantSentenceIds[$id] ?? 0) + 1;
                }
            }
        }

        if (empty($relevantSentenceIds)) {
            return "Извините, в исходном тексте нет совпадений по вашему запросу.";
        }

        // Сортируем предложения по релевантности (количеству совпадений слов)
        arsort($relevantSentenceIds);
        $bestIds = array_slice(array_keys($relevantSentenceIds), 0, 5); // Берем топ-5 предложений

        // Собираем найденный контекст в одну строку
        $context = "";
        foreach ($bestIds as $id) {
            $context .= $this->sentences[$id] . " ";
        }

        return $this->generateMarkov($context, $queryWords);
    }

    /**
     * Генератор цепи Маркова на основе найденного контекста
     */
    private function generateMarkov(string $context, array $queryWords): string
    {
        $words = $this->tokenize($context);
        if (count($words) < 2) return $context;

        $chain = [];
        for ($i = 0; $i < count($words) - 1; $i++) {
            $w1 = $words[$i];
            $w2 = $words[$i + 1];
            $chain[$w1][] = $w2;
        }

        // Стартовое слово: ищем слово из запроса, которое есть в контексте
        $currentWord = $words[0];
        foreach ($queryWords as $qw) {
            if (isset($chain[$qw])) {
                $currentWord = $qw;
                break;
            }
        }

        $result = [$currentWord];
        $maxLength = 15; // Ограничение длины ответа

        for ($i = 0; $i < $maxLength; $i++) {
            if (!isset($chain[$currentWord])) break;

            $nextOptions = $chain[$currentWord];
            $currentWord = $nextOptions[array_rand($nextOptions)];
            $result[] = $currentWord;

            // Если дошли до конца предложения в контексте, можно закончить
            if (preg_match('/[.!?]$/', $currentWord)) break;
        }

        return ucfirst(implode(' ', $result));
    }

    private function tokenize(string $text): array
    {
        $clean = mb_strtolower(preg_replace('/[^\w\s\.]/u', '', $text));
        return preg_split('/\s+/u', $clean, -1, PREG_SPLIT_NO_EMPTY);
    }
}

$bot = new SmartLiteBot();

$sourceText = include __DIR__ . '/resource/text.php';

$bot->train($sourceText);

echo "Ответ: " . $bot->ask("чуть");
