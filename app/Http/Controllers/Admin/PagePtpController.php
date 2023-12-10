<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\PagePtp;
use Exception;
use Illuminate\Http\Request;
use ZipArchive;

class PagePtpController extends Controller
{
    public function index(PagePtp $pagePtp)
    {
        try {
            $name = $pagePtp->filename_chr;
            $content = $pagePtp->content;
            $path = public_path('ptps_files/' . $name);
            file_put_contents($path, $content);
            return response()->download($path)->deleteFileAfterSend(true);
        } catch (Exception $e) {
            return redirect()->route('admin.home');
        }
    }

    public function batch(Request $request, $ids)
    {
        try {
            $selectedIds = explode(',', $ids);
            $pageptps = PagePtp::whereIn('id', $selectedIds)->get();
            $zip = new ZipArchive;
            $zipFileName = 'selected_ptps_' . time() . '.zip';
            $zipFilePath = public_path('ptps_files/' . $zipFileName);
            if ($zip->open($zipFilePath, ZipArchive::CREATE) === TRUE) {
                foreach ($pageptps as $item) {
                    $txtFileName = $item->filename_chr;
                    $txtContent = $item->content;
                    $zip->addFromString($txtFileName, $txtContent);
                }
                $zip->close();
                return response()->download($zipFilePath, $zipFileName)->deleteFileAfterSend(true);
            }
        } catch (Exception $e) {
            return redirect()->route('admin.home');
        }
    }
}
