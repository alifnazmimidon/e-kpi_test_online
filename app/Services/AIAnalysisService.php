<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAnalysisService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key');
        $this->model = (string) config('services.gemini.model', 'gemini-3.8-flash');
    }

    /**
     * 1. Analisis Prestasi Keseluruhan untuk Kad AI Analytics di HR Dashboard
     */
    public function analyzeHrOverview(): array
    {
        $settings = DB::table('kpi_settings')->first();

        // Gunakan alias mesra pengguna supaya AI tidak menyebut nama kolum teknikal database
        $evaluations = DB::table('monthly_evaluations as me')
            ->join('users as u', 'me.worker_id', '=', 'u.worker_id')
            ->leftJoin('departments as d', 'u.department_id', '=', 'd.department_id')
            ->select(
                'u.worker_id as staff_id',
                'u.full_name as staff_name',
                'd.department_name as department',
                'me.evaluation_month as month',
                'me.evaluation_year as year',
                'me.tasks_score as task_performance',
                'me.attendance_score as attendance_rating',
                'me.leader_score as leader_rating',
                'me.total_score as overall_kpi_score',
                'me.eom_badge as award_badge',
                'me.evaluation_status as submission_status'
            )
            ->orderBy('me.evaluation_year', 'desc')
            ->orderBy('me.evaluation_month', 'desc')
            ->limit(15)
            ->get();

        $payloadData = [
            'thresholds'  => $settings,
            'evaluations' => $evaluations,
        ];

        $prompt = <<<PROMPT
        You are an HR performance analyst for the E-KPI System (Infinite Loop Media Sdn Bhd).
        Analyze the KPI evaluation records in the JSON below and identify key patterns such as sudden score drops between months, pending evaluations, or top-performing staff/departments.

        System Data:
        {$this->toJson($payloadData)}

        STRICT LANGUAGE & FORMATTING RULES:
        1. Write ALL output strictly in clear, professional English. Do NOT use Malay.
        2. NEVER use database attribute names or underscores such as "leader_score", "tasks_score", "attendance_score", "total_score", or "evaluation_status". Use natural HR terms like "leader rating", "task completion", "attendance", and "overall KPI score".
        3. Keep the text concise so it fits neatly inside the dashboard card:
        - "title": Maximum 5 words.
        - "summary": 1 short sentence (maximum 18 words) referencing the actual staff/department name and score.
        - "recommendation": 1 short actionable sentence (maximum 14 words).

        Return ONLY valid JSON matching this exact structure (maximum 3 cards):
        {
        "insights": [
            {
            "tag": "CRITICAL DROP | TOP PERFORMER | PENDING ACTION",
            "title": "Short title maximum 5 words",
            "summary": "One concise sentence maximum 18 words.",
            "recommendation": "One concise action sentence maximum 14 words."
            }
        ]
        }
        PROMPT;

        $result = $this->callGemini($prompt);

        if (empty($result['insights'])) {
            $pendingEval = $evaluations->firstWhere('submission_status', 'PENDING');
            $lowestEval  = $evaluations->sortBy('overall_kpi_score')->first();
            $highestEval = $evaluations->sortByDesc('overall_kpi_score')->first();

            $fallbackInsights = [];

            if ($lowestEval) {
                $fallbackInsights[] = [
                    'tag'            => 'CRITICAL DROP',
                    'title'          => "{$lowestEval->staff_name} Needs Review",
                    'summary'        => "{$lowestEval->staff_name} ({$lowestEval->department}) recorded an overall KPI score of {$lowestEval->overall_kpi_score} in {$lowestEval->month}/{$lowestEval->year}.",
                    'recommendation' => "Schedule a 1-on-1 coaching session with the {$lowestEval->department} leader.",
                ];
            }

            if ($pendingEval) {
                $fallbackInsights[] = [
                    'tag'            => 'PENDING ACTION',
                    'title'          => "{$pendingEval->staff_name} Evaluation Pending",
                    'summary'        => "{$pendingEval->staff_name} ({$pendingEval->department}) still has a pending evaluation for {$pendingEval->month}/{$pendingEval->year}.",
                    'recommendation' => "Remind the {$pendingEval->department} leader to finalize the pending assessment.",
                ];
            }

            if ($highestEval) {
                $fallbackInsights[] = [
                    'tag'            => 'TOP PERFORMER',
                    'title'          => "{$highestEval->department} Leads Performance",
                    'summary'        => "{$highestEval->staff_name} ({$highestEval->department}) achieved an outstanding overall KPI score of {$highestEval->overall_kpi_score}.",
                    'recommendation' => "Recognize {$highestEval->staff_name} for consistent excellence and EOM eligibility.",
                ];
            }

            return [
                'insights' => $fallbackInsights,
                'source'   => 'fallback',
            ];
        }

        // Penapis keselamatan: tukar sebarang istilah database jika masih terlepas
        $replacements = [
            'leader_score'      => 'leader rating',
            'leader_rating'     => 'leader rating',
            'tasks_score'       => 'task completion score',
            'task_performance'  => 'task completion score',
            'attendance_score'  => 'attendance score',
            'attendance_rating' => 'attendance score',
            'total_score'       => 'overall KPI score',
            'overall_kpi_score' => 'overall KPI score',
            'evaluation_status' => 'submission status',
        ];

        $result['insights'] = array_map(function ($item) use ($replacements) {
            foreach (['title', 'summary', 'recommendation'] as $field) {
                if (!empty($item[$field])) {
                    $item[$field] = str_ireplace(
                        array_keys($replacements),
                        array_values($replacements),
                        $item[$field]
                    );
                }
            }
            return $item;
        }, array_slice($result['insights'], 0, 3));

        $result['source'] = 'gemini-ai';
        return $result;
    }

    /**
     * 2. Jana Draf E-mel Peringatan Automatik untuk Ketua Jabatan / Staf
     */
    /**
     * 2. Jana Draf E-mel Peringatan Automatik untuk Ketua Jabatan / Staf
     */
    public function generateReminderDraft(string $workerId, int $variation = 1): array
    {
        $user = DB::table('users as u')
            ->leftJoin('departments as d', 'u.department_id', '=', 'd.department_id')
            ->where('u.worker_id', $workerId)
            ->select('u.worker_id', 'u.full_name', 'u.email', 'u.role', 'u.kpi_score', 'd.department_name')
            ->first();

        $latestEval = DB::table('monthly_evaluations')
            ->where('worker_id', $workerId)
            ->orderByDesc('evaluation_year')
            ->orderByDesc('evaluation_month')
            ->first();

        $settings  = DB::table('kpi_settings')->first();
        $cutoffDay = $settings->cutoff_day ?? 28;
        $isLeader  = strtoupper($user->role ?? '') === 'LEADER';
        $name      = $user->full_name ?? $workerId;
        $dept      = strtoupper($user->department_name ?? 'DEPARTMENT');

        $tones = [
            1 => 'polite and supportive',
            2 => 'concise and action-oriented',
            3 => 'formal executive reminder',
        ];
        $selectedTone = $tones[(($variation - 1) % 3) + 1];

        $contextNote = $isLeader
            ? "Penerima ialah Ketua Jabatan (Department Leader) bernama {$name}. Ingatkan beliau untuk melengkapkan dan menghantar penilaian KPI bulanan semua staf di bawah jabatan {$dept} sebelum hari ke-{$cutoffDay}."
            : "Penerima ialah Staf bernama {$name} dengan skor KPI terkini {$user->kpi_score}%. Ingatkan beliau untuk menyemak prestasi KPI dan berbincang dengan Ketua Jabatan {$dept}.";

        $prompt = <<<PROMPT
Tulis satu draf e-mel peringatan dalam nada {$selectedTone} daripada pihak HR menggunakan sistem E-KPI (Infinite Loop Media Sdn Bhd).
Maklumat Penerima: {$this->toJson($user)}
Rekod Penilaian Terkini: {$this->toJson($latestEval)}
Konteks Tugasan: {$contextNote}
Variasi Ke: {$variation} (gunakan ayat yang segar dan berbeza daripada draf biasa).

Pulangkan HANYA dalam format JSON sah ini (dalam Bahasa Inggeris profesional):
{
  "subject": "Subjek e-mel yang jelas dan profesional",
  "body": "Isi kandungan e-mel lengkap bermula dengan 'Dear {$name},' dan penutup rasmi daripada HR Department, Infinite Loop Media Sdn Bhd"
}
PROMPT;

        $result = $this->callGemini($prompt);

        // 3 variasi templat peribadi jika API Gemini sesak (503) semasa klik Regenerate
        $idx = ($variation - 1) % 3;

        if ($isLeader) {
            $fallbackTemplates = [
                [
                    'subject' => "Action Required: Pending Monthly KPI Submissions ({$dept})",
                    'body'    => "Dear {$name},\n\nThis is a friendly reminder from HR to complete and submit the pending monthly KPI evaluations for your team in {$dept} before the {$cutoffDay}th of this month.\n\nYour prompt action helps us finalize company-wide performance analytics on schedule.\n\nBest regards,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
                [
                    'subject' => "Follow-Up: {$dept} Monthly KPI Evaluation Deadline",
                    'body'    => "Dear {$name},\n\nOur E-KPI system indicates that monthly staff evaluations for {$dept} are still pending submission. Kindly review and finalize all pending team assessments before the {$cutoffDay}th cutoff date.\n\nPlease reach out to HR if you require any assistance.\n\nWarm regards,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
                [
                    'subject' => "Reminder: Finalize {$dept} Team KPI Assessments",
                    'body'    => "Dear {$name},\n\nWe would like to remind you to submit the remaining monthly KPI ratings for {$dept} prior to the {$cutoffDay}th deadline so that monthly Employee of the Month (EOM) calculations can proceed smoothly.\n\nThank you for your leadership and cooperation.\n\nSincerely,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
            ];
        } else {
            $fallbackTemplates = [
                [
                    'subject' => "Performance Review Notice: Monthly KPI Follow-Up ({$dept})",
                    'body'    => "Dear {$name},\n\nWe noticed your recent KPI performance requires attention. Please coordinate with your {$dept} department leader to review your targets and action plan for this quarter.\n\nBest regards,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
                [
                    'subject' => "KPI Check-In & Coaching Session ({$dept})",
                    'body'    => "Dear {$name},\n\nAs part of our continuous performance support in {$dept}, HR encourages you to schedule a brief 1-on-1 check-in with your team leader to discuss task milestones and improvement goals.\n\nWarm regards,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
                [
                    'subject' => "Action Plan: Upcoming KPI Target Alignment ({$dept})",
                    'body'    => "Dear {$name},\n\nKindly review your latest monthly evaluation metrics on the E-KPI portal and align with your {$dept} leader on key deliverables ahead of the {$cutoffDay}th review cycle.\n\nSincerely,\nHR Department\nInfinite Loop Media Sdn Bhd",
                ],
            ];
        }

        return [
            'recipient_email' => $user->email ?? '',
            'recipient_name'  => $name,
            'recipient_role'  => strtoupper($user->role ?? 'STAFF'),
            'subject'         => $result['subject'] ?? $fallbackTemplates[$idx]['subject'],
            'body'            => $result['body'] ?? $fallbackTemplates[$idx]['body'],
        ];
    }

    /**
     * Fungsi teras menghantar permintaan HTTP ke Google Gemini API
     */
    /**
     * Fungsi teras menghantar permintaan HTTP ke Google Gemini API
     */
    /**
     * Fungsi teras menghantar permintaan HTTP ke Google Gemini API
     * dengan tukar ganti model automatik jika pelayan utama sesak (503/429)
     */
    /**
     * Fungsi teras menghantar permintaan HTTP ke Google Gemini API
     * dengan tukar ganti model automatik jika pelayan utama sesak (503/429)
     */
    /**
     * Fungsi teras menghantar permintaan HTTP ke Google Gemini API
     * dengan tukar ganti model automatik jika pelayan utama sesak (503/429) atau bermasalah
     */
    protected function callGemini(string $prompt): array
    {
        @set_time_limit(120);

        if (empty($this->apiKey)) {
            Log::warning('GEMINI_API_KEY belum ditetapkan dalam .env');
            return [];
        }

        $models = array_values(array_unique([
            'gemini-3.7-flash',
            'gemini-3.6-flash',
            'gemini-3.8-flash',
            'gemini-3.5-flash',
            'gemini-3.5-flash-lite',
            'gemini-3.1-flash-lite',
        ]));

        foreach ($models as $modelName) {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modelName}:generateContent?key={$this->apiKey}";

            try {
                $http = Http::connectTimeout(5)
                    ->timeout(15)
                    ->withHeaders([
                        'Expect' => '',
                    ])
                    ->withOptions([
                        'force_ip_resolve' => 'v4',
                        'version'          => 1.1,
                    ])
                    ->acceptJson();

                // Elak ralat sijil SSL (cURL error 60) pada XAMPP Windows
                if (app()->environment('local')) {
                    $http = $http->withoutVerifying();
                }

                $response = $http->post($url, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'      => 0.3,
                        'responseMimeType' => 'application/json',
                    ],
                ]);

                // Jika model sesak (503) atau had kuota sementara (429), terus lompat ke model sandaran
                if (in_array($response->status(), [429, 503])) {
                    Log::warning("Model {$modelName} sesak (HTTP {$response->status()}), mencuba model sandaran...");
                    continue;
                }

                // Jika model membalas ralat lain (cth: 404/500), log dan terus cuba model seterusnya
                if (!$response->successful()) {
                    Log::error('Gemini API Error', [
                        'model'  => $modelName,
                        'status' => $response->status(),
                        'body'   => $response->body(),
                    ]);
                    continue;
                }

                $rawText = data_get($response->json(), 'candidates.0.content.parts.0.text', '{}');
                $decoded = json_decode($rawText, true);

                if (is_array($decoded) && !empty($decoded)) {
                    return $decoded;
                }
            } catch (\Throwable $e) {
                Log::warning("Gemini Timeout/Exception ({$modelName}): " . $e->getMessage());
            }
        }

        return [];
    }

    protected function toJson(mixed $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}