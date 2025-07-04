<?php

namespace App\Services;

use App\Http\Requests\ExtractEmailRequest;
use App\Models\Application;
use PhpAmqpLib\Message\AMQPMessage;
use App\Services\EmailLogService;
use App\Models\EmailLog;
use Maatwebsite\Excel\Facades\Excel;

class EmailQueueService
{
    private $RabbitMQService;
    private $EmailLogService;

    //Fungsi untuk menginisialisasi service
    public function __construct(RabbitMQService $RabbitMQService, EmailLogService $EmailLogService)
    {
        $this->RabbitMQService = $RabbitMQService;
        $this->EmailLogService = $EmailLogService;
    }

    // Map prioritas string ke nilai numerik
    private $priorityMap = [
        'low' => 1,
        'medium' => 2,
        'high' => 3,
    ];

    //Fungsi untuk mengekstrak data email log berdasarkan ID
    public function extractEmailLogData(ExtractEmailRequest $request)
    {
        $id = $request->input('id');
        $emailLog = EmailLog::find($id);

        if (!$emailLog) {
            // Kembalikan response JSON standar REST API
            return response()->json([
                'success' => false,
                'message' => 'Data log email tidak ditemukan',
            ], 404);
        }

        try {
            $emailData = json_decode($emailLog->request, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Format JSON pada data log email tidak valid.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data log email berhasil di ekstrak',
                'data' => [
                    'to' => $emailData['to'] ?? '',
                    'subject' => $emailData['subject'] ?? '',
                    'content' => $emailData['content'] ?? '',
                    'priority' => $emailData['priority'] ?? '',
                    'attachment' => $emailData['attachment'] ?? [],
                    'status' => $emailLog['status'] ?? '',
                    'error_message' => $emailLog['error_message'] ?? '',
                    'secret' => $emailData['secret'] ?? '',
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat memproses data log email.',
            ], 500);
        }
    }

    //Function untuk memvalidasi setiap baris data email dari file excel
    private function validateRow(array $row, int $index): array
    {
        $priorityList = array_keys($this->priorityMap);

        $processedRow = [
            'secret' => trim($row[0] ?? null),
            'to' => trim($row[1] ?? null),
            'content' => $row[2] ?? '',
            'subject' => $row[3] ?? '',
            'priority' => strtolower(trim($row[4] ?? '')),
            'attachment' => isset($row[5]) ? array_map('trim', explode(',', $row[5])) : [],
        ];

        $errors = [];
        if (empty($processedRow['secret'])) {
            $errors[] = 'Secret key wajib diisi';
        }
        if (empty($processedRow['to'])) {
            $errors[] = 'Email penerima ("to") wajib diisi';
        } elseif (!filter_var($processedRow['to'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid untuk kolom "to"';
        }
        if (empty($processedRow['priority']) || !in_array($processedRow['priority'], $priorityList, true)) {
            $errors[] = 'Prioritas wajib diisi dan harus salah satu dari: low, medium, high';
        }
        if (!empty($processedRow['attachment'])) {
            foreach ($processedRow['attachment'] as $url) {
                if (!empty($url) && !filter_var($url, FILTER_VALIDATE_URL)) {
                    $errors[] = 'Setiap attachment harus berupa URL yang valid.';
                    break;
                }
            }
        }

        // Cek aplikasi
        if (!empty($processedRow['secret'])) {
            $application = Application::where('secret_key', $processedRow['secret'])->first();
            if (!$application) {
                $errors[] = 'Secret key tidak valid';
            } elseif ($application->status !== 'enabled') {
                $errors[] = 'Status aplikasi disabled';
            } else {
                $processedRow['application_id'] = $application->id;
            }
        }

        return [
            'row' => $index + 1,
            'data' => $processedRow,
            'errors' => $errors,
        ];
    }

    //Function untuk memparsing file excel menjadi array
    private function parseExcel($file): array
    {
        $data = Excel::toArray(new class implements \Maatwebsite\Excel\Concerns\ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $file);

        return $data[0] ?? [];
    }

    //Function untuk memproses dan memasukan email ke dalam RabbitMQ
    public function processAndQueueEmails(array $emails, string $secret)
    {
        $channel = $this->RabbitMQService->connect();

        $application = Application::where('secret_key', $secret)
            ->where('status', 'enabled')
            ->first();

        if (!$application) {
            return ['error' => 'Secret key tidak valid'];
        }

        $messages = [];
        foreach ($emails as $mail) {
            // Validasi wajib
            if (
                !isset($mail['to']) || empty($mail['to']) ||
                !isset($mail['priority']) || empty($mail['priority']) ||
                !isset($secret) || empty($secret)
            ) {
                return ['error' => 'Field "to", "priority", dan "secret" wajib diisi.'];
            }

            $emailLog = $this->EmailLogService->logEmail($mail, $application->id);
            $mail['id'] = $emailLog->id;

            try {
                $priority = $this->priorityMap[$mail['priority']] ?? 2;
                $subject = $mail['subject'] ?? '';
                $content = $mail['content'] ?? '';

                $messageData = [
                    'id' => $mail['id'],
                    'to' => $mail['to'],
                    'subject' => $subject,
                    'content' => $content,
                    'attachment' => $mail['attachment'] ?? [],
                    'priority' => $mail['priority'],
                    'secret' => $secret,
                ];

                $msg = new AMQPMessage(
                    json_encode($messageData),
                    [
                        'delivery_mode' => 2,
                        'priority' => $priority,
                    ]
                );

                $channel->basic_publish($msg, 'email_exchange', 'email');

                $messageDataReturn = $messageData;
                unset($messageDataReturn['secret']);

                $messages[] = $messageDataReturn;
            } catch (\Exception $e) {
                $this->EmailLogService->updateLog(
                    ['id' => $mail['id']],
                    'failed',
                    'Antrian error: ' . $e->getMessage(),
                    $application->id
                );
                return ['error' => 'Antrian error: ' . $e->getMessage()];
            }
        }

        return ['messages' => $messages];
    }

    //Function untuk memproses email dari file excel
    public function processEmailsFromExcel($file): array
    {
        $rows = $this->parseExcel($file);
        if (empty($rows)) {
            return ['error' => 'File Excel tidak valid atau kosong'];
        }

        $messages = [];
        $validationErrors = [];

        foreach ($rows as $index => $row) {
            // Skip header
            if ($index === 0 && strcasecmp($row[0] ?? '', 'secret') === 0) {
                continue;
            }
            // Skip row kosong
            if (empty(array_filter($row, fn($value) => trim($value) !== ''))) {
                continue;
            }

            $result = $this->validateRow($row, $index);
            if ($result['errors']) {
                $validationErrors[] = [
                    'row' => $result['row'],
                    'errors' => $result['errors'],
                ];
                continue;
            }
            $messages[] = $result['data'];
        }

        if ($validationErrors) {
            return ['validationErrors' => $validationErrors];
        }

        if (empty($messages)) {
            return ['error' => 'Tidak ada data email yang valid ditemukan dalam file Excel'];
        }

        // Urutkan berdasarkan prioritas
        usort($messages, function ($a, $b) {
            return $this->priorityMap[$b['priority']] <=> $this->priorityMap[$a['priority']];
        });

        // Kirim email ke RabbitMQ
        $result = $this->processAndQueueEmails($messages, $messages[0]['secret']);

        if (isset($result['error'])) {
            return ['error' => $result['error']];
        }

        // Return messages with id (already set in processAndQueueEmails)
        return ['messages' => $result['messages']];
    }
}