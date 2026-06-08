<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function __construct(private array $filters = []) {}

    public function query()
    {
        $query = Student::query()->with(['classroom', 'schoolYear'])->active()->orderBy('last_name');

        $f = $this->filters;
        if (!empty($f['classroom_id']))   $query->where('classroom_id', $f['classroom_id']);
        if (!empty($f['school_year_id'])) $query->where('school_year_id', $f['school_year_id']);
        if (!empty($f['search'])) {
            $s = $f['search'];
            $query->where(fn ($q) =>
                $q->where('first_name', 'like', "%$s%")
                  ->orWhere('last_name', 'like', "%$s%")
                  ->orWhere('registration_number', 'like', "%$s%"));
        }

        return $query;
    }

    public function headings(): array
    {
        return ['Matricule', 'Prénom', 'Nom', 'Né(e) le', 'Genre', 'Classe',
                'Année scolaire', 'Tuteur', 'Téléphone tuteur', 'Email tuteur', 'Inscrit le'];
    }

    public function map($s): array
    {
        return [
            $s->registration_number,
            $s->first_name,
            $s->last_name,
            $s->date_of_birth?->format('d/m/Y'),
            $s->gender === 'F' ? 'Fille' : 'Garçon',
            $s->classroom?->name,
            $s->schoolYear?->name,
            $s->parent_name,
            $s->parent_phone,
            $s->parent_email,
            $s->enrolled_at?->format('d/m/Y'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                  'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4F46E5']]],
        ];
    }

    public function title(): string
    {
        return 'Élèves';
    }
}
