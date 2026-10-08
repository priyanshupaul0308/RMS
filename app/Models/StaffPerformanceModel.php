<?php

declare(strict_types=1);

namespace App\Models;

/**
 * StaffPerformanceModel - Staff reviews, attendance scoring, orders handled, feedback and ratings
 */
class StaffPerformanceModel extends BaseModel
{
    protected $table            = 'staff_performance_reviews';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'branch_id',
        'reviewer_id',
        'review_period',
        'rating_attendance',
        'rating_punctuality',
        'rating_order_accuracy',
        'rating_hospitality',
        'overall_score',
        'strengths',
        'areas_for_improvement',
        'goals',
        'review_date',
    ];

    protected $validationRules = [
        'user_id'               => 'required|is_natural_no_zero',
        'review_period'         => 'required|min_length[3]|max_length[40]',
        'rating_attendance'     => 'required|is_natural|greater_than_equal_to[1]|less_than_equal_to[5]',
        'rating_punctuality'    => 'required|is_natural|greater_than_equal_to[1]|less_than_equal_to[5]',
        'rating_order_accuracy' => 'required|is_natural|greater_than_equal_to[1]|less_than_equal_to[5]',
        'rating_hospitality'    => 'required|is_natural|greater_than_equal_to[1]|less_than_equal_to[5]',
    ];

    /**
     * Get 360-degree performance metrics for a staff member.
     *
     * @return array<string, mixed>
     */
    public function getStaffMetrics(int $userId, int $branchId): array
    {
        // 1. Attendance Metrics (last 30 days)
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $attModel = new StaffAttendanceModel();
        $totalDays = $attModel->where('user_id', $userId)
            ->where('date >=', $thirtyDaysAgo)
            ->countAllResults();
        
        $lateDays = $attModel->where('user_id', $userId)
            ->where('date >=', $thirtyDaysAgo)
            ->where('status', 'late')
            ->countAllResults();

        $hoursRow = $attModel->selectSum('total_hours', 'sum_hours')
            ->selectSum('overtime_hours', 'sum_ot')
            ->where('user_id', $userId)
            ->where('date >=', $thirtyDaysAgo)
            ->first();
        
        $totalHours = (float) ($hoursRow['sum_hours'] ?? 0);
        $overtimeHours = (float) ($hoursRow['sum_ot'] ?? 0);

        // 2. Orders Handled (last 30 days)
        $orderCount = $this->db->table('orders')
            ->groupStart()
                ->where('waiter_id', $userId)
                ->orWhere('cashier_id', $userId)
            ->groupEnd()
            ->where('created_at >=', $thirtyDaysAgo . ' 00:00:00')
            ->countAllResults();

        $orderSalesRow = $this->db->table('orders')
            ->selectSum('final_total', 'total_sales')
            ->groupStart()
                ->where('waiter_id', $userId)
                ->orWhere('cashier_id', $userId)
            ->groupEnd()
            ->where('created_at >=', $thirtyDaysAgo . ' 00:00:00')
            ->where('status', 'completed')
            ->get()
            ->getRowArray();
        $totalSales = (float) ($orderSalesRow['total_sales'] ?? 0);

        // 3. Customer Feedback (last 30 days)
        $staffFeedback = $this->db->table('customer_feedback')
            ->join('orders', 'orders.id = customer_feedback.order_id', 'inner')
            ->groupStart()
                ->where('orders.waiter_id', $userId)
                ->orWhere('orders.cashier_id', $userId)
            ->groupEnd()
            ->where('customer_feedback.created_at >=', $thirtyDaysAgo . ' 00:00:00')
            ->selectAvg('customer_feedback.rating', 'avg_rating')
            ->selectCount('customer_feedback.id', 'total_feedbacks')
            ->get()
            ->getRowArray();

        if (!empty($staffFeedback) && (int) ($staffFeedback['total_feedbacks'] ?? 0) > 0) {
            $avgFeedback = round((float) $staffFeedback['avg_rating'], 1);
            $totalFeedbacks = (int) $staffFeedback['total_feedbacks'];
        } else {
            $branchFeedback = $this->db->table('customer_feedback')
                ->where('created_at >=', $thirtyDaysAgo . ' 00:00:00')
                ->selectAvg('rating', 'avg_rating')
                ->selectCount('id', 'total_feedbacks')
                ->get()
                ->getRowArray();
            $avgFeedback = round((float) ($branchFeedback['avg_rating'] ?? 5.0), 1);
            $totalFeedbacks = (int) ($branchFeedback['total_feedbacks'] ?? 0);
        }

        // 4. Latest Review (ORDER BY review_date DESC, id DESC to ensure newest review is returned)
        $latestReview = $this->db->table($this->table)
            ->where('user_id', $userId)
            ->orderBy('review_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()
            ->getFirstRow('array');

        return [
            'total_attendance_days' => $totalDays,
            'late_days'             => $lateDays,
            'total_hours'           => $totalHours,
            'overtime_hours'        => $overtimeHours,
            'orders_handled'        => $orderCount,
            'total_sales'           => $totalSales,
            'avg_customer_feedback' => $avgFeedback,
            'total_feedbacks'       => $totalFeedbacks,
            'latest_review'         => $latestReview,
        ];
    }

    /**
     * Get review history for a staff member.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getReviewsForUser(int $userId): array
    {
        return $this->select('staff_performance_reviews.*, reviewer.first_name as reviewer_fname, reviewer.last_name as reviewer_lname')
            ->join('users as reviewer', 'reviewer.id = staff_performance_reviews.reviewer_id', 'left')
            ->where('staff_performance_reviews.user_id', $userId)
            ->orderBy('staff_performance_reviews.review_date', 'DESC')
            ->orderBy('staff_performance_reviews.id', 'DESC')
            ->findAll();
    }
}
