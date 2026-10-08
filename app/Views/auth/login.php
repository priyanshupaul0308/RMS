<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Sign in to the RMS Restaurant Management System">
  <title><?= esc($pageTitle ?? 'Sign In') ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= base_url('assets/css/auth.css') ?>">
</head>
<body class="auth-body">

  <div class="auth-wrapper">

    <!-- Left panel -->
    <div class="auth-panel-left">
      <div class="auth-brand">
        <div class="brand-icon">&#127860;</div>
        <h1 class="brand-name">RMS</h1>
        <p class="brand-tagline">Restaurant Management System</p>
      </div>
      <div class="auth-illustration">
        <div class="stats-card">
          <span class="stat-icon">&#128200;</span>
          <div>
            <div class="stat-value">99.9%</div>
            <div class="stat-label">Uptime</div>
          </div>
        </div>
        <div class="stats-card">
          <span class="stat-icon">&#127860;</span>
          <div>
            <div class="stat-value">40+</div>
            <div class="stat-label">Modules</div>
          </div>
        </div>
        <div class="stats-card">
          <span class="stat-icon">&#128101;</span>
          <div>
            <div class="stat-value">6</div>
            <div class="stat-label">Role Levels</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right panel: login form -->
    <div class="auth-panel-right">
      <div class="auth-form-container">

        <div class="auth-header">
          <h2>Welcome back</h2>
          <p>Sign in to your account to continue</p>
        </div>

        <!-- Flash messages -->
        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-error" role="alert">
            <span class="alert-icon">&#9888;</span>
            <?= esc(session()->getFlashdata('error')) ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success" role="alert">
            <span class="alert-icon">&#10003;</span>
            <?= esc(session()->getFlashdata('success')) ?>
          </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
          <div class="alert alert-error" role="alert">
            <ul class="error-list">
              <?php foreach ($errors as $err): ?>
                <li><?= esc($err) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <!-- Login form -->
        <form action="<?= site_url('login') ?>" method="post" id="login-form" novalidate>
          <?= csrf_field() ?>

          <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-wrapper">
              <span class="input-icon">&#9993;</span>
              <input
                type="email"
                id="email"
                name="email"
                class="form-input"
                value="<?= esc(old('email')) ?>"
                placeholder="admin@example.com"
                autocomplete="email"
                required
              >
            </div>
          </div>

          <div class="form-group">
            <label for="password" class="form-label">
              Password
              <a href="<?= site_url('forgot-password') ?>" class="forgot-link" tabindex="-1">Forgot password?</a>
            </label>
            <div class="input-wrapper">
              <span class="input-icon">&#128274;</span>
              <input
                type="password"
                id="password"
                name="password"
                class="form-input"
                placeholder="Enter your password"
                autocomplete="current-password"
                required
              >
              <button type="button" class="password-toggle" id="toggle-password" aria-label="Toggle password visibility">
                &#128065;
              </button>
            </div>
          </div>

          <div class="form-check">
            <input type="checkbox" id="remember" name="remember" value="1" class="check-input">
            <label for="remember" class="check-label">Keep me signed in</label>
          </div>

          <button type="submit" class="btn-primary" id="login-btn">
            <span class="btn-text">Sign In</span>
            <span class="btn-spinner" hidden>&#9696;</span>
          </button>

        </form>

        <div class="auth-footer">
          <p>Enterprise Restaurant Management &amp; POS &copy; <?= date('Y') ?></p>
        </div>

      </div>
    </div>

  </div><!-- /.auth-wrapper -->

  <script>
    // Password visibility toggle
    document.getElementById('toggle-password').addEventListener('click', function () {
      var pwField = document.getElementById('password');
      pwField.type = pwField.type === 'password' ? 'text' : 'password';
    });

    // Show spinner on submit
    document.getElementById('login-form').addEventListener('submit', function () {
      var btn     = document.getElementById('login-btn');
      var spinner = btn.querySelector('.btn-spinner');
      var text    = btn.querySelector('.btn-text');
      text.hidden    = true;
      spinner.hidden = false;
      btn.disabled   = true;
    });
  </script>
</body>
</html>
