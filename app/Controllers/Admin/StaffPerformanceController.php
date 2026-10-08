<?php

declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\StaffPerformanceModel;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * StaffPerformanceController - 360-degree staff evaluations, attendance, orders handled, feedback and reviews
 */
class StaffPerformanceController extends BaseController
{
    protected StaffPerformanceModel $perfModel;
    protected UserModel $userModel;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ): void {
        parent::initController($request, $response, $logger);
        $this->perfModel = new StaffPerformanceModel();
        $this->userModel = new UserModel();
    }

    /**
     * Staff Performance Leaderboard & Scorecards
     */
    public function index(): string
    {
        $this->authorize('performance.view');

        $restaurantId = (int)($this->currentRestaurantId ?? 1);
        $branchModel = new \App\Models\BranchModel();
        $branches = $branchModel->getActiveBranches($restaurantId);

        $selectedBranch = $this->request->getGet('branch');
        if ($selectedBranch === null || $selectedBranch === '') {
            $selectedBranch = 'all'; // Default to All Branches so all staff members are visible
        }

        $builder = $this->userModel
            ->select('users.*, roles.name AS role_name, roles.slug AS role_slug, branches.name AS branch_name')
            ->join('roles',    'roles.id = users.role_id',      'left')
            ->join('branches', 'branches.id = users.branch_id', 'left')
            ->where('users.restaurant_id', $restaurantId)
            ->where('users.is_active', 1);

        if ($selectedBranch !== 'all') {
            $bId = (int) $selectedBranch;
            $builder->groupStart()
                ->where('users.branch_id', $bId)
                ->orWhere('users.branch_id IS NULL', null, false)
                ->orWhere('users.branch_id', 0)
                ->groupEnd();
        }

        $staffMembers = $builder->orderBy('roles.id', 'ASC')->orderBy('users.first_name', 'ASC')->findAll();

        $performanceRoster = [];
        $overallBranchScore = 0.0;
        $count = 0;

        foreach ($staffMembers as $staff) {
            $sBranchId = !empty($staff['branch_id']) ? (int) $staff['branch_id'] : ($this->currentBranchId ?? 1);
            $metrics = $this->perfModel->getStaffMetrics((int) $staff['id'], $sBranchId);
            $score = $metrics['latest_review'] ? (float) $metrics['latest_review']['overall_score'] : 4.5;
            $overallBranchScore += $score;
            $count++;

            $performanceRoster[] = [
                'user'    => $staff,
                'metrics' => $metrics,
                'score'   => $score,
            ];
        }

        // Sort by performance score descending
        usort($performanceRoster, fn($a, $b) => $b['score'] <=> $a['score']);

        $avgBranchScore = $count > 0 ? round($overallBranchScore / $count, 2) : 5.0;

        return $this->renderView('admin/performance/index', [
            'pageTitle'         => 'Staff Performance & Appraisals',
            'performanceRoster' => $performanceRoster,
            'avgBranchScore'    => $avgBranchScore,
            'totalStaff'        => $count,
            'branches'          => $branches,
            'selectedBranch'    => $selectedBranch,
        ]);
    }

    /**
     * Submit Manager Review
     */
    public function storeReview(): ResponseInterface
    {
        $this->authorize('performance.manage');

        $rules = [
            'user_id'               => 'required|is_natural_no_zero',
            'review_period'         => 'required|min_length[3]|max_length[40]',
            'rating_attendance'     => 'required|is_natural|greater_than_equal_to[1]|less_than_equal_to[5]',
            'rating_punctuality'    => 'required|is_natural|greater_than_equal_to[1]|less_than_equal_to[5]',
            'rating_order_accuracy' => 'required|is_natural|greater_than_equal_to[1]|less_than_equal_to[5]',
            'rating_hospitality'    => 'required|is_natural|greater_than_equal_to[1]|less_than_equal_to[5]',
        ];

        if ($errors = $this->validatePost($rules)) {
            return $this->jsonError('Validation error', 422, $errors);
        }

        $userId = (int) $this->request->getPost('user_id');
        $targetUser = $this->userModel->find($userId);
        if (!$targetUser) {
            return $this->jsonError('Employee not found.', 404);
        }

        $rAtt   = (int) $this->request->getPost('rating_attendance');
        $rPunc  = (int) $this->request->getPost('rating_punctuality');
        $rOrder = (int) $this->request->getPost('rating_order_accuracy');
        $rHosp  = (int) $this->request->getPost('rating_hospitality');

        // Weighted overall score out of 5.0
        $overall = round(($rAtt * 0.25) + ($rPunc * 0.20) + ($rOrder * 0.30) + ($rHosp * 0.25), 2);

        $reviewPeriod = trim((string) $this->request->getPost('review_period'));
        $reviewId     = (int) $this->request->getPost('review_id');
        $branchId     = !empty($targetUser['branch_id']) ? (int) $targetUser['branch_id'] : ($this->currentBranchId ?? 1);

        $data = [
            'user_id'               => $userId,
            'branch_id'             => $branchId,
            'reviewer_id'           => $this->currentUserId ?? 1,
            'review_period'         => $reviewPeriod,
            'rating_attendance'     => $rAtt,
            'rating_punctuality'    => $rPunc,
            'rating_order_accuracy' => $rOrder,
            'rating_hospitality'    => $rHosp,
            'overall_score'         => $overall,
            'strengths'             => trim((string) $this->request->getPost('strengths')),
            'areas_for_improvement' => trim((string) $this->request->getPost('areas_for_improvement')),
            'goals'                 => trim((string) $this->request->getPost('goals')),
            'review_date'           => date('Y-m-d'),
        ];

        // Check if updating an existing appraisal for this user and period (or by review_id)
        $existing = null;
        if ($reviewId > 0) {
            $existing = $this->perfModel->find($reviewId);
        }
        if (!$existing) {
            $existing = $this->perfModel->where('user_id', $userId)
                                        ->where('review_period', $reviewPeriod)
                                        ->first();
        }

        if ($existing) {
            $ok = $this->perfModel->update($existing['id'], $data);
            if ($ok === false) {
                $errs = $this->perfModel->errors();
                return $this->jsonError('Failed to update review: ' . (!empty($errs) ? implode(', ', $errs) : 'Database update failed'), 500);
            }
            $savedId = (int) $existing['id'];
        } else {
            $savedId = $this->perfModel->insert($data);
            if ($savedId === false) {
                $errs = $this->perfModel->errors();
                return $this->jsonError('Failed to save review: ' . (!empty($errs) ? implode(', ', $errs) : 'Database insert failed'), 500);
            }
        }

        $this->perfModel->writeAudit(
            'performance',
            $existing ? 'update' : 'create',
            'staff_performance_reviews',
            $savedId,
            $existing ?: null,
            $data,
            "Appraisal review saved for {$targetUser['first_name']} {$targetUser['last_name']} (Score: {$overall}/5.0)"
        );

        return $this->jsonSuccess([
            'id'            => $savedId,
            'overall_score' => $overall,
            'user_id'       => $userId,
            'staff_name'    => $targetUser['first_name'] . ' ' . $targetUser['last_name'],
            'review_period' => $reviewPeriod,
        ], 'Staff performance appraisal review saved successfully.');
    }

    /**
     * Get appraisal review history for a staff member (JSON)
     */
    public function history(int $userId): ResponseInterface
    {
        $this->authorize('performance.view');

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->jsonError('Staff member not found.', 404);
        }

        $reviews = $this->perfModel->getReviewsForUser($userId);

        return $this->jsonSuccess([
            'user'    => [
                'id'         => $user['id'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'email'      => $user['email'],
            ],
            'reviews' => $reviews,
        ]);
    }
}
