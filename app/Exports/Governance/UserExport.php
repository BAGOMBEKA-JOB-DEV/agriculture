<?php

namespace App\Exports\Governance;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UserExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $allowedRoles = ['super_admin', 'vendor', 'trader', 'buyer'];
        $query = User::with(['roles'])->whereHas('roles', fn($q) => $q->whereIn('slug', $allowedRoles));

        if (!empty($this->filters['q'])) {
            $search = '%' . $this->filters['q'] . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ILIKE', $search)
                  ->orWhere('email', 'ILIKE', $search);
            });
        }

        if (!empty($this->filters['role'])) {
            $query->whereHas('roles', fn($q) => $q->where('slug', $this->filters['role']));
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['location'])) {
            $query->where('location', 'ILIKE', '%' . $this->filters['location'] . '%');
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Role',
            'Status',
            'Location',
            'Joined Date',
            'Verified At'
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->role ?? ($user->roles->first()?->name ?? 'N/A'),
            ucfirst($user->status),
            $user->location ?? 'Global',
            $user->created_at->format('Y-m-d H:i:s'),
            $user->email_verified_at ? $user->email_verified_at->format('Y-m-d H:i:s') : 'Pending'
        ];
    }
}
