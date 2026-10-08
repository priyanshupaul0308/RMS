<div class="page-header">
  <div>
    <h1 class="page-title">Tax Rate Management</h1>
    <p class="page-subtitle">Configure GST, VAT, service taxes, and compound tax rules</p>
  </div>
  <div class="page-actions">
    <a href="<?= site_url('admin/settings') ?>" class="btn btn-secondary">&larr; Back to Settings</a>
  </div>
</div>

<div class="grid-2">
  <!-- Tax Rates Table -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Configured Tax Rates (<?= count($taxRates) ?>)</div>
    </div>
    <div class="card-body" style="padding: 0;">
      <table class="rms-table">
        <thead>
          <tr>
            <th>Tax Name</th>
            <th>Rate</th>
            <th>Type</th>
            <th>Category</th>
            <th>Compound</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($taxRates)): ?>
            <tr>
              <td colspan="5" class="text-center text-muted" style="padding: 2rem 1rem;">
                No tax rates defined yet. Use the form to configure your first rate.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($taxRates as $t): ?>
              <tr>
                <td class="td-bold"><?= esc($t['name']) ?></td>
                <td>
                  <span class="badge badge-info" style="font-size: .85rem;">
                    <?= esc(number_format((float)$t['rate'], 2)) ?>%
                  </span>
                </td>
                <td>
                  <span class="status-badge <?= $t['type'] === 'inclusive' ? 'status-pending' : 'status-active' ?>">
                    <?= esc(ucfirst($t['type'])) ?>
                  </span>
                </td>
                <td class="text-capitalize"><?= esc($t['applies_to']) ?></td>
                <td><?= !empty($t['is_compound']) ? 'Yes' : 'No' ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Add New Tax Rate Form -->
  <div class="card">
    <div class="card-header">
      <div class="card-title">Add Tax Rate</div>
    </div>
    <div class="card-body">
      <form action="<?= site_url('admin/settings/tax/save') ?>" method="POST">
        <?= csrf_field() ?>

        <?php $errors = session()->getFlashdata('errors') ?? []; ?>

        <div class="form-group">
          <label class="form-label" for="name">Tax Name / Label <span class="form-required">*</span></label>
          <input type="text" name="name" id="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" 
                 placeholder="e.g. CGST 2.5%, SGST 2.5%, VAT 5%" required>
          <?php if (isset($errors['name'])): ?>
            <div class="form-error"><?= esc($errors['name']) ?></div>
          <?php endif; ?>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="rate">Percentage Rate (%) <span class="form-required">*</span></label>
              <input type="number" step="0.01" name="rate" id="rate" class="form-control <?= isset($errors['rate']) ? 'is-invalid' : '' ?>" 
                     placeholder="e.g. 5.00" required>
              <?php if (isset($errors['rate'])): ?>
                <div class="form-error"><?= esc($errors['rate']) ?></div>
              <?php endif; ?>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="type">Tax Type <span class="form-required">*</span></label>
              <select name="type" id="type" class="form-control" required>
                <option value="exclusive">Exclusive (Added on top)</option>
                <option value="inclusive">Inclusive (Embedded in menu price)</option>
              </select>
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="applies_to">Applies To</label>
              <select name="applies_to" id="applies_to" class="form-control">
                <option value="all">All Items</option>
                <option value="food">Food Only</option>
                <option value="beverage">Beverages Only</option>
                <option value="service">Service Charge / Facilities</option>
              </select>
            </div>
          </div>
          <div class="form-col">
            <div class="form-group">
              <label class="form-label" for="is_compound">Calculation Mode</label>
              <select name="is_compound" id="is_compound" class="form-control">
                <option value="0">Standard Simple Tax</option>
                <option value="1">Compound Tax (Calculated on subtotal + previous taxes)</option>
              </select>
            </div>
          </div>
        </div>

        <button type="submit" class="btn btn-primary mt-2">
          Save Tax Rate
        </button>
      </form>
    </div>
  </div>
</div>
