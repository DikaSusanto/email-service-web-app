<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendEmailRequest;
use App\Http\Requests\ExtractEmailRequest;
use App\Http\Requests\ExcelRequest;
use App\Services\EmailQueueService;
use App\Models\Application;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;

class EmailQueueController extends Controller
{
    private $EmailQueueService;

    //Constructor untuk menginisialisasi service 
    public function __construct(EmailQueueService $EmailQueueService)
    {
        $this->EmailQueueService = $EmailQueueService;
    }

    //Function untuk mengirim email ke dalam queue RabbitMQ
    public function sendEmails(SendEmailRequest $request)
    {
        $data = $request->json()->all();

        // Ambiil aplikasi berdasarkan secret key
        $application = Application::where('secret_key', $data['secret'])->first();

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Secret key tidak valid',
            ], 422);
        }

        if ($application->status !== 'enabled') {
            return response()->json([
                'success' => false,
                'message' => 'Status aplikasi disabled',
            ], 422);
        }

        usort($data['mail'], function ($a, $b) {
            $priorityMap = ['low' => 1, 'medium' => 2, 'high' => 3];
            return $priorityMap[$b['priority']] <=> $priorityMap[$a['priority']];
        });

        try {
            $result = $this->EmailQueueService->processAndQueueEmails($data['mail'], $data['secret']);
        } catch (AMQPIOException | AMQPConnectionClosedException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat terhubung ke RabbitMQ. Hubungi administrator.',
            ], 503);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal.',
            ], 500);
        }

        if (isset($result['error'])) {
            $status = str_contains($result['error'], 'RabbitMQ') ? 503 : 422;
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], $status);
        }

        // Setelah berhasil, log sudah dibuat di service, tinggal return
        return response()->json([
            'success' => true,
            'message' => 'Email masuk kedalam antrian',
            'messages' => $result['messages'],
        ]);
    }

    //Function untuk mengirim email dari file excel ke dalam queue RabbitMQ
    public function sendEmailsFromExcel(ExcelRequest $request)
    {
        $file = $request->file('excel_file');

        if (!$file) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada file yang diunggah',
            ], 400);
        }

        try {
            $result = $this->EmailQueueService->processEmailsFromExcel($file);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan internal. Tidak dapat terhubung ke RabbitMQ',
            ], 500);
        }

        if (isset($result['error'])) {
            $status = str_contains($result['error'], 'RabbitMQ') ? 503 : 400;
            return response()->json([
                'success' => false,
                'message' => $result['error'],
            ], $status);
        }

        if (isset($result['validationErrors'])) {
            return response()->json([
                'success' => false,
                'messages' => $result['validationErrors'],
            ], 422);
        }

        // Log sudah dibuat di service, tinggal return
        return response()->json([
            'success' => true,
            'message' => 'Email dari file excel masuk kedalam antrian',
            'messages' => $result['messages'],
        ]);
    }

    //Function untuk mengambil data email log berdasarkan id
    public function extractEmailData(ExtractEmailRequest $request)
    {
        $data = $this->EmailQueueService->extractEmailLogData($request);
        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }
}