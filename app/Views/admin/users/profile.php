<div class="page-header">
  <div>
    <h1 class="page-title">My Profile</h1>
    <p class="page-subtitle">View your account information and change security credentials</p>
  </div>
</div>

<div class="grid-2">
  <!-- Profile Information -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">User Account Details</div>
    </div>
    <div class="card-body">
      <div class="d-flex align-center gap-2 mb-3">
        <div class="user-avatar" style="width: 56px; height: 56px; font-size: 1.5rem;">
          <?= esc(mb_strtoupper(mb_substr($user['first_name'] ?? 'U', 0, 1))) ?>
        </div>
        <div>
          <h2 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">
            <?= esc(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?>
          </h2>
          <div class="text-secondary text-small"><?= esc($user['email']) ?></div>
          <div class="mt-1">
            <span class="badge badge-primary"><?= esc($user['role_name'] ?? 'Staff') ?></span>
            <span class="text-muted text-xs ms-auto" style="margin-left: .5rem;">
              Branch: <?= esc($user['branch_name'] ?? 'All Branches / Head Office') ?>
            </span>
          </div>
        </div>
      </div>

      <table class="rms-table" style="background: transparent;">
        <tbody>
          <tr>
            <td style="width: 140px;" class="text-muted">Employee ID</td>
            <td class="td-bold"><?= esc($user['employee_id'] ?: 'Not Assigned') ?></td>
          </tr>
          <tr>
            <td class="text-muted">Phone Number</td>
            <td class="td-bold"><?= esc($user['phone'] ?: 'None') ?></td>
          </tr>
          <tr>
            <td class="text-muted">Joining Date</td>
            <td class="td-bold"><?= !empty($user['joining_date']) ? esc(date('M d, Y', strtotime($user['joining_date']))) : 'N/A' ?></td>
          </tr>
          <tr>
            <td class="text-muted">Last Login</td>
            <td class="td-bold"><?= !empty($user['last_login_at']) ? esc(date('M d, Y H:i:s', strtotime($user['last_login_at']))) : 'Never' ?></td>
          </tr>
          <tr>
            <td class="text-muted">Last Login IP</td>
            <td class="td-bold"><?= esc($user['last_login_ip'] ?: '—') ?></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Security: Change Password -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Security & Password</div>
    </div>
    <div class="card-body">
      <form action="<?= site_url('admin/users/change-password') ?>" method="POST">
        <?= csrf_field() ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <div class="form-group">
          <label class="form-label" for="current_password">Current Password <span class="form-required">*</span></label>
          <input type="password" name="current_password" id="current_password" 
                 class="form-control <?= isset($errors['current_password']) ? 'is-invalid' : '' ?>" required>
          <?php if (isset($errors['current_password'])): ?>
            <div class="form-error"><?= esc($errors['current_password']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label" for="new_password">New Password <span class="form-required">*</span></label>
          <input type="password" name="new_password" id="new_password" 
                 class="form-control <?= isset($errors['new_password']) ? 'is-invalid' : '' ?>" minlength="8" required>
          <div class="form-hint">Must be at least 8 characters long</div>
          <?php if (isset($errors['new_password'])): ?>
            <div class="form-error"><?= esc($errors['new_password']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-group">
          <label class="form-label" for="confirm_password">Confirm New Password <span class="form-required">*</span></label>
          <input type="password" name="confirm_password" id="confirm_password" 
                 class="form-control <?= isset($errors['confirm_password']) ? 'is-invalid' : '' ?>" minlength="8" required>
          <?php if (isset($errors['confirm_password'])): ?>
            <div class="form-error"><?= esc($errors['confirm_password']) ?></div>
          <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary mt-2">
          Update Password
        </button>
      </form>
    </div>
  </div>
</div>
