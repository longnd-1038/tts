<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TTSFreeController extends Controller
{
    public function tts(Request $request)
    {
        $text = $request->get('text') ?? 'Nhập text nào anh trai';
        if ($request->hasFile('text_file')) {
            $uploadedFile = $request->file('text_file');

            // Check if the uploaded file is a valid .txt file
            if ($uploadedFile->getClientOriginalExtension() === 'txt') {
                $text = file_get_contents($uploadedFile->getRealPath());
            }
        }

        $text = str_replace("\n", ' ', $text);

        $apiKey = 'PtZerLXSlyfOF5nx25fgXu7TPedpDVm8'; // Add your API key here

        try {
            $url = 'https://ttsfree.com/api/v1/tts';
            $data = [
                'text' => $text,
                'voiceService' => 'servicebin',
                'voiceID' => 'vi-VN2',
                'voiceSpeed' => '0',
            ];
            $headers = [
                'Content-Type' => 'application/json', // Adjust content type as needed
                'apikey' => $apiKey
            ];
            $client = new \GuzzleHttp\Client();

            $response = $client->post($url, [
                'headers' => $headers,
                'json' => $data,
            ]);
            $audioData = json_decode($response->getBody()->getContents(), true)['audioData'] ?? null;
            $mp3FilePath = 'dinhlongit.mp3'; // Provide the desired file path
            Storage::disk('public')->put($mp3FilePath, base64_decode($audioData));

            //return response()->json(['file_mp3' => env('APP_URL').'/storage/' . $mp3FilePath, true, 200]);
            return response()->json(['file_mp3' => $audioData, true, 200]);
        } catch (\Exception $e) {
            // Handle exceptions, e.g., log errors or throw custom exceptions
            return null;
        }
    }
}
