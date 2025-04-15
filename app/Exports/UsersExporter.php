<?php

namespace App\Exports;

use App\Models\User;
use App\Models\Role;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class UsersExporter implements FromQuery, WithHeadings, WithMapping, WithEvents, WithCustomStartCell
{
    public function query()
    {
        return User::query()->with('role');
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Role',
            'Email Verified At',
            'Created At',
            'Updated At'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role?->name ?? 'No Role',
            $user->email_verified_at?->format('Y-m-d H:i:s') ?? 'Not Verified',
            $user->created_at->format('Y-m-d H:i:s'),
            $user->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Get all roles
                $roles = Role::pluck('name')->toArray();
                
                // The column where role dropdown will be (D is the 4th column)
                $roleColumn = 'D';
                
                // Get the last row number
                $lastRow = $event->sheet->getHighestRow();
                
                // Create the validation
                $validation = $event->sheet->getCell("{$roleColumn}2")->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                $validation->setAllowBlank(false);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(true);
                $validation->setErrorTitle('Input error');
                $validation->setError('Value is not in list.');
                $validation->setPromptTitle('Pick from list');
                $validation->setPrompt('Please pick a role from the drop-down list.');
                $validation->setFormula1('"'.implode(',', $roles).'"');

                // Apply validation to all cells in the role column
                for ($i = 2; $i <= $lastRow; $i++) {
                    $event->sheet->getCell("{$roleColumn}{$i}")->setDataValidation(clone $validation);
                }

                // Auto-size columns
                foreach (range('A', 'G') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            },
        ];
    }
} 