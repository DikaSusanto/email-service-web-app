<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Application;
use App\Services\RabbitMQService;
use App\Services\EngineService;
use App\Services\EmailLogService;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Exception\AMQPIOException;
use PhpAmqpLib\Exception\AMQPConnectionClosedException;

class EmailEngine extends Command
{
    // Mendefinisikan signature command yang digunakan di terminal
    protected $signature = 'rabbitmq:consume';
    protected $description = 'Mengonsumsi Email dari RabbitMQ dan memroses untuk dikirim';

    private $RabbitMQService;
    private $EngineService;
    private $EmailLogService;

    // Function constructor untuk menginisialisasi service yang diperlukan
    public function __construct(RabbitMQService $RabbitMQService, EngineService $EngineService, EmailLogService $EmailLogService)
    {
        parent::__construct();
        $this->RabbitMQService = $RabbitMQService;
        $this->EngineService = $EngineService;
        $this->EmailLogService = $EmailLogService;
    }

    // Function untuk memulai konsumsi pesan dari RabbitMQ
    public function handle()
    {
        $this->info('Email Engine dimulai...');
        try {
            $this->RabbitMQService->connect();
            $this->RabbitMQService->consume('email_queue', [$this, 'processEmail']);
            $this->RabbitMQService->wait();
        } catch (AMQPIOException | AMQPConnectionClosedException $e) {
            $this->error('Tidak dapat terhubung ke RabbitMQ. Hubungi administrator.');
            return 1;
        } catch (\Exception $e) {
            $this->error('Terjadi kesalahan internal.');
            return 1;
        }
    }

    // Function untuk memvalidasi aplikasi berdasarkan secret key
    private function validateApplication($secret)
    {
        if (empty($secret)) {
            return [null, 'Format pesan invalid: Secret key tidak ada.'];
        }
        $application = Application::where('secret_key', $secret)->first();
        if (!$application) {
            return [null, 'Secret key invalid'];
        }
        return [$application, null];
    }

    // Function untuk memproses pesan email yang diterima dari RabbitMQ
    public function processEmail(AMQPMessage $msg)
    {
        try {
            $data = json_decode($msg->getBody(), true, 512, JSON_THROW_ON_ERROR);
            if (!$data || !is_array($data)) {
                $this->error("Data email dari queue kosong atau tidak valid.");
                return;
            }
        } catch (\JsonException $e) {
            $this->error("Malformed JSON: " . $e->getMessage());
            return;
        }

        $secret = isset($data['secret']) ? $data['secret'] : null;
        $to = isset($data['to']) ? $data['to'] : null;
        $priority = isset($data['priority']) ? $data['priority'] : null;

        // Validasi wajib
        if (empty($secret) || empty($to) || empty($priority)) {
            $this->error("Field 'secret', 'to', dan 'priority' wajib diisi.");
            return;
        }

        [$application, $error] = $this->validateApplication($secret);
        if ($error) {
            $this->error($error);
            return;
        }

        // Untuk logika pengiriman email
        try {
            if (!$to) {
                throw new \Exception("Field 'to' tidak ditemukan pada data email.");
            }
            $this->EngineService->sendEmail($data);
            $this->EmailLogService->updateLog($data, 'success', null, $application->id);
            $this->info("Email sukses terkirim kepada: " . $to);
        } catch (\Exception $e) {
            $this->EmailLogService->updateLog($data, 'failed', $e->getMessage(), $application->id);
            $this->error("Gagal mengirim email kepada: " . ($to ?? '-') . " - " . $e->getMessage());
        }
    }
}