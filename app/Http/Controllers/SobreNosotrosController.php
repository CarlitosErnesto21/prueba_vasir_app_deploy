<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteSetting;
use App\Models\CompanyValue;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class SobreNosotrosController extends Controller
{
    public function index()
    {
        // Forzar recarga de datos sin cache
        $mission = SiteSetting::where('key', 'company_mission')->first();
        $vision = SiteSetting::where('key', 'company_vision')->first();
        $description = SiteSetting::where('key', 'company_description')->first();
        
        // Obtener valores corporativos desde la base de datos
        $companyValues = CompanyValue::getAllValues();
        
        // Debug: Log los valores para verificar
        Log::info('SobreNosotros - Mission: ' . ($mission ? $mission->value : 'NULL'));
        Log::info('SobreNosotros - Vision: ' . ($vision ? $vision->value : 'NULL'));
        Log::info('SobreNosotros - Description: ' . ($description ? $description->value : 'NULL'));
        Log::info('SobreNosotros - Company Values Count: ' . $companyValues->count());
        
        $siteSettings = [
            'mission' => $mission ? $mission->value : '',
            'vision' => $vision ? $vision->value : '',
            'description' => $description ? $description->value : '',
        ];
        
        return Inertia::render('VistasClientes/SobreNosotros', [
            'siteSettings' => $siteSettings,
            'companyValues' => $companyValues
        ]);
    }
}
