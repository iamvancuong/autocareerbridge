<?php

namespace App\Services;

use App\Models\Company;
use App\Models\University;
use App\Models\Collaboration;
use Illuminate\Database\Eloquent\Collection;

class CollaborationService
{
    /**
     * Company sends a collaboration request to a University
     */
    public function sendRequest(Company $company, University $university): Collaboration
    {
        // Check if a request already exists
        $existing = Collaboration::where('company_id', $company->id)
                                ->where('university_id', $university->id)
                                ->first();

        if ($existing) {
            if ($existing->status === 'approved') {
                throw new \Exception('Hai bên đã đang hợp tác với nhau rồi.');
            }
            if ($existing->status === 'pending') {
                throw new \Exception('Đã có yêu cầu hợp tác đang chờ xử lý.');
            }
            if ($existing->status === 'rejected') {
                // Cooldown: 30 days after rejection
                $cooldownEnd = $existing->updated_at->addDays(30);
                if (now()->lt($cooldownEnd)) {
                    $daysLeft = (int) now()->diffInDays($cooldownEnd, false);
                    throw new \Exception("Yêu cầu đã bị từ chối. Bạn có thể gửi lại sau {$daysLeft} ngày nữa (sau {$cooldownEnd->format('d/m/Y')}).");
                }
                // Cooldown passed — reuse the record
                $initiatedBy = request()->user()?->role ?? 'company';
                $existing->update(['status' => 'pending', 'initiated_by' => $initiatedBy]);
                return $existing;
            }
        }

        // Record who initiated: 'company' or 'university'
        $initiatedBy = request()->user()?->role ?? 'company';

        return Collaboration::create([
            'company_id' => $company->id,
            'university_id' => $university->id,
            'status' => 'pending',
            'initiated_by' => $initiatedBy,
        ]);
    }

    /**
     * University approves a collaboration request
     */
    public function approveRequest(Collaboration $collaboration): bool
    {
        if ($collaboration->status !== 'pending') {
            throw new \Exception('Only pending requests can be approved.');
        }

        return $collaboration->update(['status' => 'approved']);
    }

    /**
     * University rejects a collaboration request
     */
    public function rejectRequest(Collaboration $collaboration): bool
    {
        if ($collaboration->status !== 'pending') {
            throw new \Exception('Only pending requests can be rejected.');
        }

        return $collaboration->update(['status' => 'rejected']);
    }

    /**
     * Check if a Company and University are actively collaborating
     */
    public function isCollaborating(Company $company, University $university): bool
    {
        return Collaboration::where('company_id', $company->id)
                            ->where('university_id', $university->id)
                            ->where('status', 'approved')
                            ->exists();
    }

    /**
     * Get all approved universities for a company
     */
    public function getCollaboratingUniversities(Company $company): Collection
    {
        return University::whereHas('collaborations', function ($query) use ($company) {
            $query->where('company_id', $company->id)
                  ->where('status', 'approved');
        })->get();
    }
}
