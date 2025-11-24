<?php

namespace App\Services;

use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Log;

class YandexReviewsParser
{
    public function parseReviews(string $url, int $limit = 10): array
    {
        Log::info('YandexReviewsParser: начат парсинг', ['url' => $url, 'limit' => $limit]);

        try {
            Log::debug('YandexReviewsParser: создаем Browsershot инстанс');
            
            $html = Browsershot::url($url)
                ->setBinPath('/usr/bin/chromium')
                ->setOption('newHeadless', true)
                ->setOption('headless', 'new')
                ->addChromiumArguments([
                    '--no-sandbox',
                    '--disable-setuid-sandbox',
                    '--disable-blink-features=AutomationControlled',
                    '--disable-infobars',
                    '--window-size=1200,800',
                    '--start-maximized',
                    '--disable-web-security',
                    '--disable-features=IsolateOrigins,site-per-process',
                    '--allow-running-insecure-content',
                    '--user-agent=Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36',
                ])
                ->ignoreHttpsErrors()
                ->waitUntilNetworkIdle(false)
                ->delay(3000)
                ->timeout(60)
                ->setDelay(3000)
                ->bodyHtml();

            Log::debug('YandexReviewsParser: получен HTML', ['html_length' => strlen($html), 'html_sample' => substr($html, 0, 500)]);

            if (empty($html)) {
                Log::warning('YandexReviewsParser: получен пустой HTML');
                return [
                    'success' => false,
                    'error'   => 'Получен пустой HTML от Yandex',
                    'reviews' => [],
                    'total'   => 0
                ];
            }

            if (str_contains($html, 'Вы не робот') || str_contains($html, 'checkcaptcha') || str_contains($html, 'smart-captcha')) {
                Log::warning('YandexReviewsParser: обнаружена CAPTCHA');
                return [
                    'success' => false,
                    'error'   => 'Yandex требует CAPTCHA. Подожди 10 минут и попробуй снова.',
                    'reviews' => [],
                    'total'   => 0
                ];
            }

            Log::info('YandexReviewsParser: начинаем парсинг HTML');
            $result = $this->parseHtml($html, $limit);
            Log::info('YandexReviewsParser: парсинг завершен', ['success' => $result['success'], 'reviews_count' => count($result['reviews']['reviews'] ?? [])]);

            return $result;

        } catch (\Exception $e) {
            Log::error('YandexReviewsParser: ошибка при парсинге', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'error'   => 'Ошибка загрузки страницы',
                'reviews' => [],
                'total'   => 0
            ];
        }
    }

    private function parseHtml(string $html, int $limit): array
    {
        Log::debug('parseHtml: начат парсинг HTML', ['html_length' => strlen($html), 'limit' => $limit]);

        $reviews = [];

        $overallRating = $this->extractOverallRating($html);
        $totalReviews  = $this->extractTotalReviews($html);

        Log::debug('parseHtml: общие данные', ['overall_rating' => $overallRating, 'total_reviews' => $totalReviews]);

        preg_match_all(
            '#<div[^>]+class="[^"]*business-review-view[^"]*"[^>]*itemprop=["\']review["\'][^>]*>.*?(?=<div class="business-review-view"[^>]*itemprop=["\']review["\']|$)#s',
            $html,
            $reviewBlocks
        );

        Log::debug('parseHtml: найдено блоков отзывов', ['blocks_count' => count($reviewBlocks[0])]);

        foreach ($reviewBlocks[0] as $index => $reviewHtml) {
            if (count($reviews) >= $limit) {
                Log::debug('parseHtml: достигнут лимит отзывов', ['limit' => $limit]);
                break;
            }

            $review = [
                'author' => $this->extractAuthor($reviewHtml),
                'rating' => $this->extractRating($reviewHtml),
                'date'   => $this->extractDate($reviewHtml),
                'text'   => $this->extractText($reviewHtml),
                'likes'  => $this->extractLikes($reviewHtml),
            ];

            Log::debug("parseHtml: обработан отзыв $index", $review);

            if (!empty($review['author']) || !empty($review['text'])) {
                $reviews[] = $review;
                Log::debug("parseHtml: отзыв $index добавлен в результат");
            } else {
                Log::debug("parseHtml: отзыв $index пропущен (пустые данные)");
            }
        }

        $result = [
            'success' => true,
            'reviews' => [
                'overall_rating' => $overallRating,
                'total_reviews'  => $totalReviews,
                'reviews'        => $reviews,
                'parsed_count'   => count($reviews),
            ],
            'total' => $totalReviews,
        ];

        Log::info('parseHtml: парсинг завершен', ['parsed_reviews' => count($reviews), 'total_reviews' => $totalReviews]);

        return $result;
    }

    private function extractOverallRating(string $html): ?string
    {
        $rating = preg_match('#itemprop=["\']ratingValue["\'][^>]*content=["\']([\d.]+)["\']#', $html, $m)
            ? $m[1]
            : null;
        Log::debug('extractOverallRating', ['rating' => $rating]);
        return $rating;
    }

    private function extractTotalReviews(string $html): int
    {
        $total = preg_match('#itemprop=["\']ratingCount["\'][^>]*content=["\'](\d+)["\']#', $html, $m)
            ? (int)$m[1]
            : 0;
        Log::debug('extractTotalReviews', ['total' => $total]);
        return $total;
    }

    private function extractAuthor(string $html): string
    {
        $author = preg_match('#itemprop=["\']name["\'][^>]*>([^<]+)</span>#', $html, $m)
            ? trim(strip_tags($m[1]))
            : 'Аноним';
        Log::debug('extractAuthor', ['author' => $author]);
        return $author;
    }

    private function extractRating(string $html): int
    {
        if (preg_match('#aria-label=["\']Оценка (\d) из 5["\']#iu', $html, $m)) {
            $rating = (int)$m[1];
            Log::debug('extractRating: найдено через aria-label', ['rating' => $rating]);
            return $rating;
        }

        preg_match_all('#class="[^"]*business-rating-badge-view__star[^"]*_full[^"]*"#', $html, $stars);
        if (!empty($stars[0])) {
            $rating = count($stars[0]);
            Log::debug('extractRating: найдено через подсчет звезд', ['rating' => $rating, 'stars_count' => count($stars[0])]);
            return $rating;
        }

        if (preg_match('#itemprop=["\']ratingValue["\'][^>]*content=["\'](\d)["\']#', $html, $m)) {
            $rating = (int)$m[1];
            Log::debug('extractRating: найдено через itemprop', ['rating' => $rating]);
            return $rating;
        }

        Log::debug('extractRating: рейтинг не найден, возвращаем 0');
        return 0;
    }

    private function extractDate(string $html): string
    {
        if (preg_match('#<span[^>]*>(\d+ (?:января|февраля|марта|апреля|мая|июня|июля|августа|сентября|октября|ноября|декабря))</span>#', $html, $m)) {
            $date = $this->convertRussianDate($m[1]);
            Log::debug('extractDate: найдена русская дата', ['original' => $m[1], 'converted' => $date]);
            return $date;
        }
        if (preg_match('#datePublished" content="(\d{4}-\d{2}-\d{2})#', $html, $m)) {
            Log::debug('extractDate: найдена дата в формате ISO', ['date' => $m[1]]);
            return $m[1];
        }
        Log::debug('extractDate: дата не найдена, используем текущую');
        return now()->format('Y-m-d');
    }

    private function extractText(string $html): string
    {
        if (preg_match('#<span class="[^"]*spoiler-view__text-container[^"]*"[^>]*>(.*?)</span>#s', $html, $m)) {
            $text = trim(preg_replace('/\s+/', ' ', strip_tags($m[1])));
            Log::debug('extractText: текст найден', ['text_length' => strlen($text), 'text_sample' => substr($text, 0, 100)]);
            return $text;
        }
        Log::debug('extractText: текст не найден');
        return '';
    }

    private function extractLikes(string $html): int
    {
        $likes = preg_match('#business-reactions-view__counter["\'][^>]*>(\d+)<#', $html, $m) ? (int)$m[1] : 0;
        Log::debug('extractLikes', ['likes' => $likes]);
        return $likes;
    }

    private function convertRussianDate(string $dateStr): string
    {
        $months = [
            'января' => '01', 'февраля' => '02', 'марта' => '03', 'апреля' => '04',
            'мая' => '05', 'июня' => '06', 'июля' => '07', 'августа' => '08',
            'сентября' => '09', 'октября' => '10', 'ноября' => '11', 'декабря' => '12',
        ];

        $dateStr = mb_strtolower($dateStr);
        foreach ($months as $rus => $num) {
            if (str_contains($dateStr, $rus)) {
                $date = str_replace($rus, $num, $dateStr);
                break;
            }
        }

        $result = preg_match('#(\d+)\s+(\d{2})#', $date, $m)
            ? date('Y') . '-' . $m[2] . '-' . str_pad($m[1], 2, '0', STR_PAD_LEFT)
            : now()->format('Y-m-d');

        Log::debug('convertRussianDate', ['original' => $dateStr, 'converted' => $result]);

        return $result;
    }
}