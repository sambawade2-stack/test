<?php

namespace App\Http\Controllers;

use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

class SettingsController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('role:' . User::rolesFor('settings'))];
    }

    public function index(): View
    {
        return view('settings.index', [
            'schoolYears' => SchoolYear::orderByDesc('name')->get(),
            'activeYear'  => SchoolYear::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_name'    => 'required|string|max:100',
            'school_phone'   => 'nullable|string|max:30',
            'school_email'   => 'nullable|email|max:100',
            'school_address' => 'nullable|string|max:200',
        ]);

        // Mise à jour du fichier .env
        $this->updateEnv([
            'SCHOOL_NAME'    => '"' . $data['school_name'] . '"',
            'SCHOOL_PHONE'   => '"' . ($data['school_phone'] ?? '') . '"',
            'SCHOOL_EMAIL'   => '"' . ($data['school_email'] ?? '') . '"',
            'SCHOOL_ADDRESS' => '"' . ($data['school_address'] ?? '') . '"',
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Informations de l\'établissement mises à jour.');
    }

    public function updateFees(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'school_year_id'  => 'required|exists:school_years,id',
            'inscription_fee' => 'required|numeric|min:0',
            'monthly_fee'     => 'required|numeric|min:0',
        ]);

        SchoolYear::find($data['school_year_id'])->update([
            'inscription_fee' => $data['inscription_fee'],
            'monthly_fee'     => $data['monthly_fee'],
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Tarifs mis à jour avec succès.');
    }

    private function updateEnv(array $values): void
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        foreach ($values as $key => $value) {
            if (str_contains($envContent, "$key=")) {
                $envContent = preg_replace("/^$key=.*/m", "$key=$value", $envContent);
            } else {
                $envContent .= "\n$key=$value";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
