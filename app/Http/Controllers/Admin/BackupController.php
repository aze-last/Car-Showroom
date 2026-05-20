<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BackupService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function download(BackupService $backupService): BinaryFileResponse
    {
        $this->authorize('access-admin');

        $path = $backupService->create();

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
