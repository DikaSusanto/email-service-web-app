<?php

namespace App\Http\Controllers;

use App\Services\EmailLogService;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailLogController extends Controller
{
    protected $emailLogService;

    public function __construct(EmailLogService $emailLogService)
    {
        $this->emailLogService = $emailLogService;
    }

    // Function untuk menampilkan data log di halaman 
    public function index()
    {
        $sent = $this->emailLogService->countByStatus('success');
        $failed = $this->emailLogService->countByStatus('failed');
        $total = $this->emailLogService->countAll();

        return Inertia::render('Home', [
            'data' => [
                'sent' => $sent,
                'failed' => $failed,
                'total' => $total,
            ],
        ]);
    }

    // Function untuk menampilkan data log email dengan fitur search dan orderBy
    public function integrasi(Request $request)
    {
        $search = $request->query('search', null);
        $orderBy = $request->query('orderBy', 'desc');
        $emailLogs = $this->emailLogService->getEmailLogs(search: $search, orderBy: $orderBy);

        // Pastikan response API selalu dalam format JSON standar SPA/API
        return response()->json([
            'message' => $emailLogs->isEmpty() ? 'Log email tidak ditemukan' : 'Log email berhasil ditemukan',
            'emailLogs' => $emailLogs,
        ]);
    }

    // Function untuk delete log email berdasarkan ID yang dipilih
    public function deleteAll(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
        ]);

        $ids = $request->input('ids');
        if (count($ids) === 0) {
            return response()->json([
                'message' => 'Tidak ada log email yang dipilih untuk dihapus.',
            ], 422);
        }

        $this->emailLogService->deleteEmailLogsByIds($ids);
        return response()->json([
            'message' => 'Log email yang dipilih berhasil dihapus.',
        ]);
    }
}