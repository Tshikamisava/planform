<?php

namespace App\Policies;

use App\Models\Dcr;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DcrPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view DCRs
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Dcr $dcr): bool
    {
        // Users can view their own DCRs, assigned DCRs, or if they are DOM/Admin
        return $user->id === $dcr->author_id ||
               $user->id === $dcr->recipient_id ||
               $user->id === $dcr->decision_maker_id ||
               $user->isDecisionMaker() ||
               $user->isAdministrator();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAuthor() || $user->isAdministrator();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Dcr $dcr): bool
    {
        // Authors can update their own DCRs if they are in draft status
        // Admins can update any DCR
        if ($user->isAdministrator()) {
            return true;
        }

        if ($user->id === $dcr->author_id && $dcr->status === 'draft') {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Dcr $dcr): bool
    {
        // Only admins can delete DCRs
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Dcr $dcr): bool
    {
        // Only DOMs can approve DCRs assigned to them or if they are the decision maker
        return ($user->isDecisionMaker() && $user->id === $dcr->decision_maker_id) ||
               $user->isAdministrator();
    }

    /**
     * Determine whether the user can reject the model.
     */
    public function reject(User $user, Dcr $dcr): bool
    {
        return $this->approve($user, $dcr);
    }

    /**
     * Determine whether the user can complete the model.
     */
    public function complete(User $user, Dcr $dcr): bool
    {
        // Only recipients can complete DCRs assigned to them
        return ($user->isRecipient() && $user->id === $dcr->recipient_id) ||
               $user->isAdministrator();
    }

    /**
     * Determine whether the user can close the model.
     */
    public function close(User $user, Dcr $dcr): bool
    {
        // Only admins can close DCRs
        return $user->isAdministrator();
    }

    /**
     * Determine whether the user can add impact assessment.
     */
    public function addImpactAssessment(User $user, Dcr $dcr): bool
    {
        // Only DOMs can add impact assessments
        return ($user->isDecisionMaker() && $user->id === $dcr->decision_maker_id) ||
               $user->isAdministrator();
    }

    /**
     * Determine whether the user can upload documents.
     */
    public function uploadDocuments(User $user, Dcr $dcr): bool
    {
        // Authors can upload to their own DCRs
        // Recipients can upload to assigned DCRs
        // Admins can upload to any DCR
        return $user->isAdministrator() ||
               ($user->isAuthor() && $user->id === $dcr->author_id) ||
               ($user->isRecipient() && $user->id === $dcr->recipient_id);
    }
}
