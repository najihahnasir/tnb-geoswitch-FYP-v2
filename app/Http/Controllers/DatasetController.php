<?php

namespace App\Http\Controllers;
use App\Models\Switchgear;
use App\Models\Dataset;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;
use League\Csv\Exception;
use Illuminate\Http\Request;

class DatasetController extends Controller
{
    public function showUploadForm() {
        return view('dataset');
    }

    public function handleUpload(Request $request) {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv_file');
        //to save file
        $filename = 'dataset_' . time() . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        // $path = $file->storeAs('datasets', $filename); // ✅

        // $filePath = storage_path('app/' . $path); // ✅ FULL path to file
        
        // $csvData = array_map('str_getcsv', file($filePath)); // ✅ Correct usage
         // Save to storage/app/datasets
         $destinationPath = storage_path('app/datasets');
         if (!file_exists($destinationPath)) {
             mkdir($destinationPath, 0755, true); // create if not exists
         }
 
         $file->move($destinationPath, $filename);
         $filePath = $destinationPath . '/' . $filename;
 
         if (!file_exists($filePath)) {
             return back()->with('error', '❌ File failed to save at: ' . $filePath);
         }
 
        // dd($path); 
         // ✅ Confirm the file exists (optional for debugging)
        // if (!file_exists($filePath)) {
        //     return back()->with('error', 'File was not saved properly.');
        // }

        // Parse CSV
        $csvData = array_map('str_getcsv', file(storage_path('app/' . $path)));
        $headers = array_map('trim', $csvData[0]);
        unset($csvData[0]); // Remove headers row

        $new = 0; 
        $duplicate = 0;

        foreach ($csvData as $row) {
            $row = array_map('trim', $row);
            $data = array_combine($headers, $row);

            // Check if exists
            $exists = Switchgear::where('functional_location', $data['Functional Location'])
                ->where('report_date', $data['Report Date'])
                ->where('defect_description_1', $data['Defect Description 1'])
                ->exists();

            if ($exists) {
                $duplicate++;
                continue;
            }

            // Insert
            Switchgear::create([
                'functional_location' => $data['Functional Location'],
                'report_date' => $data['Report Date'],
                'defect_description_1' => $data['Defect Description 1'],
                'defect_description_2' => $data['Defect Description 2'] ?? null,
                'defect_from' => $data['Defect From'] ?? null,
                'tev_us_in_db' => $data['TEV/US In DB'] ?? null,
                'hotspot_delta_t_in_c' => $data['Hotspot ∆T In ⁰C'] ?? null,
                'switchgear_type' => $data['Switchgear Type'] ?? null,
                'switchgear_brand' => $data['Switchgear Brand'] ?? null,
                'substation_name' => $data['Substation Name'] ?? null,
                'defect_owner' => $data['Defect Owner'] ?? null,
            ]);

            $new++;
        }

        // Log upload
        Dataset::create([
            'dataset_name' => $filename,
            'dataset_type' => 'csv',
            'dataset_file' => $path
        ]);

        return back()->with('success', "✅ Upload complete: {$new} new, {$duplicate} duplicates.");
    }
}