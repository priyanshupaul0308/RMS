<div style="display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 60vh; text-align: center; padding: 2rem;">
  <div style="font-size: 5rem; font-weight: 800; background: linear-gradient(135deg, #3b82f6, #8b5cf6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; margin-bottom: 1rem; line-height: 1;">
    404
  </div>
  <h2 style="font-size: 1.8rem; font-weight: 700; margin-bottom: 0.75rem; color: #f8fafc;">
    Page Not Found or Access Restricted
  </h2>
  <p style="max-width: 500px; color: #94a3b8; font-size: 1rem; margin-bottom: 2rem; line-height: 1.6;">
    The page you are looking for does not exist, has been moved, or your assigned staff role does not have authorization to view this resource.
  </p>
  <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
    <a href="javascript:history.back()" class="btn btn-secondary">
      &larr; Go Back
    </a>
    <?php if (session()->has('user_id')): ?>
      <?php if (session('role_slug') === 'waiter' || session('role_slug') === 'cashier'): ?>
        <a href="<?= site_url('admin/pos') ?>" class="btn btn-primary">
          Open POS Terminal
        </a>
      <?php else: ?>
        <a href="<?= site_url('admin/dashboard') ?>" class="btn btn-primary">
          Back to Dashboard
        </a>
      <?php endif; ?>
    <?php else: ?>
      <a href="<?= site_url('login') ?>" class="btn btn-primary">
        Return to Login
      </a>
    <?php endif; ?>
  </div>
</div>
