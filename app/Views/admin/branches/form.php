<div class="page-header">
  <div>
    <h1 class="page-title"><?= esc($pageTitle) ?></h1>
    <p class="page-subtitle"><?= $isEdit ? 'Update branch address and operational profile' : 'Add a new restaurant branch outlet' ?></p>
  </div>
  <div class="page-actions">
    <a href="<?= site_url('admin/branches') ?>" class="btn btn-secondary">&larr; Back to Branches</a>
  </div>
</div>

<div class="card" style="max-width: 800px;">
  <div class="card-header">
    <div class="card-title"><?= $isEdit ? 'Edit Branch Details' : 'New Branch Setup' ?></div>
  </div>
  <div class="card-body">
    <form action="<?= $isEdit ? site_url('admin/branches/update/' . $branch['id']) : site_url('admin/branches/store') ?>" method="POST">
      <?= csrf_field() ?>

      <?php $errors = session()->getFlashdata('errors') ?? []; ?>

      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="name">Branch Name <span class="form-required">*</span></label>
            <input type="text" name="name" id="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                   value="<?= esc(old('name', $branch['name'] ?? '')) ?>" placeholder="e.g. Downtown Flagship" required>
            <?php if (isset($errors['name'])): ?>
              <div class="form-error"><?= esc($errors['name']) ?></div>
            <?php endif; ?>
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="code">Branch Code</label>
            <input type="text" name="code" id="code" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" 
                   value="<?= esc(old('code', $branch['code'] ?? '')) ?>" placeholder="e.g. BR-01">
            <?php if (isset($errors['code'])): ?>
              <div class="form-error"><?= esc($errors['code']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="phone">Phone Number</label>
            <input type="tel" name="phone" id="phone" class="form-control" 
                   value="<?= esc(old('phone', $branch['phone'] ?? '')) ?>" placeholder="+91 9876543210">
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" 
                   value="<?= esc(old('email', $branch['email'] ?? '')) ?>" placeholder="branch@example.com">
            <?php if (isset($errors['email'])): ?>
              <div class="form-error"><?= esc($errors['email']) ?></div>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="address">Street Address</label>
        <textarea name="address" id="address" class="form-control" rows="2" placeholder="Full street address"><?= esc(old('address', $branch['address'] ?? '')) ?></textarea>
      </div>

      <div class="form-row">
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="city">City</label>
            <input type="text" name="city" id="city" class="form-control" 
                   value="<?= esc(old('city', $branch['city'] ?? '')) ?>" placeholder="e.g. Mumbai">
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="state">State / Province</label>
            <input type="text" name="state" id="state" class="form-control" 
                   value="<?= esc(old('state', $branch['state'] ?? '')) ?>" placeholder="e.g. Maharashtra">
          </div>
        </div>
        <div class="form-col">
          <div class="form-group">
            <label class="form-label" for="postal_code">Postal / PIN Code</label>
            <input type="text" name="postal_code" id="postal_code" class="form-control" 
                   value="<?= esc(old('postal_code', $branch['postal_code'] ?? '')) ?>" placeholder="400001">
          </div>
        </div>
      </div>

      <div class="d-flex align-center gap-1 mt-3">
        <button type="submit" class="btn btn-primary">
          <?= $isEdit ? 'Save Changes' : 'Create Branch' ?>
        </button>
        <a href="<?= site_url('admin/branches') ?>" class="btn btn-ghost">Cancel</a>
      </div>
    </form>
  </div>
</div>
