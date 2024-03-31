<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Request;
use Inertia\Inertia;
use App\Models\Fee;
use App\Models\User;
use App\Models\History;
use App\Http\Resources\FeeResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;


class FileUploadController extends Controller
{
    public function submitFees(Request $request, User $student)
    {
        
        $validateFile = $request->validate([
            'file' => 'required',
            'reference' => 'required'
        ]);

        if($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = $file->getClientOriginalName();
            $path = 'public/images/' . $fileName;
            Storage::disk('local')->put($path, file_get_contents($file));
        }
        $history = History::create([
            'student_id' => $request->student['id'],
            'name' => $request->student['name'],
            'meta' => $request->fees,
            'file' => $validateFile['file'],
            'status' => 'pending',
            'reference' => $validateFile['reference']
        ]);

        return redirect()->back();
    }
}
