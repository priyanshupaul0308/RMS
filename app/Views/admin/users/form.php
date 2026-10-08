<div class="page-header">
  <div>
    <h1 class="page-title"><?= esc($pageTitle) ?></h1>
    <p class="page-subtitle"><?= $isEdit ? 'Update staff member credentials and permissions' : 'Create a new staff member account' ?></p>
  </div>
  <div class="page-actions">
    <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">
      &larr; Back to Users
    </a>
  </div>
</div>

<div class="card" style="max-width: 800px;">
  <div class="card-header">
    <div class="card-title"><?= $isEdit ? 'Edit User Details' : 'New User Information' ?></div>
  </div>
  <div class="card-body">
    <form action="<?= $isEdit ? site_url('admin/users/update/' . $user['id']) : site_url('admin/users/store') ?>" method="POST">
      <?= csrf_field() ?>

      <?php $errors = session()->getFlashdata('errors') ?? []; ?>

      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="first_name">First Name <span class="form-required">*</span></label>
            <input type="text" name="first_name" id="first_name" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" 
                   value="<?= esc(old('first_name', $user['first_name'] ?? '')) ?>" required>
            <?php if (isset($errors['first_name'])): ?>
              <div class="form-error"><?= esc($errors['first_name']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="last_name">Last Name</label>
            <input type="text" name="last_name" id="last_name" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" 
                   value="<?= esc(old('last_name', $user['last_name'] ?? '')) ?>">
            <?php if (isset($errors['last_name'])): ?>
              <div class="form-error"><?= esc($errors['last_name']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="email">Email Address <span class="form-required">*</span></label>
            <input type="email" name="email" id="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                   value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
            <?php if (isset($errors['email'])): ?>
              <div class="form-error"><?= esc($errors['email']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" 
                   value="<?= esc(old('phone', $user['phone'] ?? '')) ?>" placeholder="+91 9876543210">
            <?php if (isset($errors['phone'])): ?>
              <div class="form-error"><?= esc($errors['phone']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="role_id">System Role <span class="form-required">*</span></label>
            <select name="role_id" id="role_id" class="form-control <?= isset($errors['role_id']) ? 'is-invalid' : '' ?>" required>
              <option value="">Select Role</option>
              <?php foreach ($roles as $r): ?>
                <option value="<?= esc($r['id']) ?>" <?= (int)old('role_id', $user['role_id'] ?? 0) === (int)$r['id'] ? 'selected' : '' ?>>
                  <?= esc($r['name']) ?> (<?= esc($r['slug']) ?>)
                </option>
              <?php endforeach; ?>
            </select>
            <?php if (isset($errors['role_id'])): ?>
              <div class="form-error"><?= esc($errors['role_id']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="branch_id">Assigned Branch</label>
            <select name="branch_id" id="branch_id" class="form-control <?= isset($errors['branch_id']) ? 'is-invalid' : '' ?>">
              <option value="">All Branches / Head Office</option>
              <?php foreach ($branches as $b): ?>
                <option value="<?= esc($b['id']) ?>" <?= (string)old('branch_id', $user['branch_id'] ?? '') === (string)$b['id'] ? 'selected' : '' ?>>
                  <?= esc($b['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="form-hint">Leave blank for multi-branch administrators</div>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="employee_id">Employee ID</label>
            <input type="text" name="employee_id" id="employee_id" class="form-control <?= isset($errors['employee_id']) ? 'is-invalid' : '' ?>" 
                   value="<?= esc(old('employee_id', $user['employee_id'] ?? '')) ?>" placeholder="e.g. EMP-001">
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="joining_date">Joining Date</label>
            <input type="date" name="joining_date" id="joining_date" class="form-control" 
                   value="<?= esc(old('joining_date', $user['joining_date'] ?? '')) ?>">
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="password">
          Password <?= $isEdit ? '<span class="text-muted text-xs">(leave blank to keep current)</span>' : '<span class="form-required">*</span>' ?>
        </label>
        <input type="password" name="password" id="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" 
               <?= $isEdit ? '' : 'required' ?> minlength="8" placeholder="Minimum 8 characters">
        <?php if (isset($errors['password'])): ?>
          <div class="form-error"><?= esc($errors['password']) ?></div>
        <?php endif; ?>
      </div>

      <div class="d-flex align-center gap-1 mt-3">
        <button type="submit" class="btn btn-primary">
          <?= $isEdit ? 'Save Changes' : 'Create User' ?>
        </button>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>
</div>
