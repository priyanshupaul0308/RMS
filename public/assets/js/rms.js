/**
 * RMS – Main Application JavaScript
 * Handles: sidebar toggle, live clock, dropdown menus,
 *          flash auto-dismiss, AJAX notification polling,
 *          CSRF-aware fetch helper.
 */
(function () {
  'use strict';

  /* ── CSRF-aware fetch wrapper ─────────────────────────────────────────── */
  window.rmsPost = function (url, data) {
    var payload = Object.assign({}, data);
    payload[window.RMS.csrfName] = window.RMS.csrfHash;

    return fetch(url, {
      method:  'POST',
      headers: {
        'Content-Type':  'application/json',
        'X-CSRF-TOKEN':  window.RMS.csrfHash,
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify(payload),
    }).then(function (r) {
      // Refresh CSRF hash from response header if present
      var newHash = r.headers.get('X-CSRF-Token');
      if (newHash && window.RMS) {
        window.RMS.csrfHash = newHash;
        document.querySelectorAll('input[name="' + window.RMS.csrfName + '"]').forEach(function(inp) {
          inp.value = newHash;
        });
      }
      return r.json();
    });
  };

  // Ensure all standard forms carry the latest CSRF hash upon submit
  document.addEventListener('submit', function (e) {
    if (window.RMS && window.RMS.csrfName && window.RMS.csrfHash && e.target && e.target.tagName === 'FORM' && (e.target.method || '').toUpperCase() === 'POST') {
      var csrfInput = e.target.querySelector('input[name="' + window.RMS.csrfName + '"]');
      if (csrfInput) {
        csrfInput.value = window.RMS.csrfHash;
      }
    }
  }, true);

  window.rmsGet = function (url) {
    return fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    }).then(function (r) { return r.json(); });
  };

  /* ── Live Clock ───────────────────────────────────────────────────────── */
  var clockEl = document.getElementById('live-clock');
  var modalClockEl = document.getElementById('modal-clock-time');
  function updateClock() {
    var now = new Date();
    var h = now.getHours(), m = now.getMinutes(), s = now.getSeconds();
    var ampm = h >= 12 ? 'PM' : 'AM';
    var h12 = h % 12 || 12;
    var timeStr = pad(h12) + ':' + pad(m) + ':' + pad(s) + ' ' + ampm;
    if (clockEl) {
      clockEl.textContent = timeStr + '  ' +
        now.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' });
    }
    if (modalClockEl) {
      modalClockEl.textContent = timeStr;
    }
  }
  function pad(n) { return n < 10 ? '0' + n : n; }
  updateClock();
  setInterval(updateClock, 1000);

  /* ── Sidebar Toggle ───────────────────────────────────────────────────── */
  var sidebar              = document.getElementById('sidebar');
  var sidebarToggle        = document.getElementById('sidebar-toggle');
  var desktopSidebarToggle = document.getElementById('desktop-sidebar-toggle');
  var mobileToggle         = document.getElementById('mobile-sidebar-toggle');

  function toggleSidebarCollapse() {
    if (!sidebar) return;
    sidebar.classList.toggle('collapsed');
    localStorage.setItem('rms_sidebar_collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', toggleSidebarCollapse);
  }
  if (desktopSidebarToggle) {
    desktopSidebarToggle.addEventListener('click', toggleSidebarCollapse);
  }

  // Restore collapsed state (default to expanded if not set)
  if (sidebar && localStorage.getItem('rms_sidebar_collapsed') === '1') {
    sidebar.classList.add('collapsed');
  }

  // Clicking on brand when collapsed expands it
  var brand = document.querySelector('.sidebar-brand');
  if (brand && sidebar) {
    brand.addEventListener('click', function (e) {
      if (sidebar.classList.contains('collapsed') && e.target !== sidebarToggle) {
        toggleSidebarCollapse();
      }
    });
  }

  var backdrop = document.getElementById('sidebar-backdrop');
  if (mobileToggle && sidebar) {
    mobileToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      var isOpen = sidebar.classList.toggle('mobile-open');
      document.body.classList.toggle('sidebar-open', isOpen);
    });

    if (backdrop) {
      backdrop.addEventListener('click', function () {
        sidebar.classList.remove('mobile-open');
        document.body.classList.remove('sidebar-open');
      });
    }

    // Close on outside click
    document.addEventListener('click', function (e) {
      if (sidebar.classList.contains('mobile-open') &&
          !sidebar.contains(e.target) && e.target !== mobileToggle) {
        sidebar.classList.remove('mobile-open');
        document.body.classList.remove('sidebar-open');
      }
    });

    // Auto close sidebar when a navigation item is tapped on mobile
    sidebar.querySelectorAll('.nav-item').forEach(function (link) {
      link.addEventListener('click', function () {
        if (window.innerWidth <= 992) {
          sidebar.classList.remove('mobile-open');
          document.body.classList.remove('sidebar-open');
        }
      });
    });
  }

  /* ── Mark active nav item ─────────────────────────────────────────────── */
  var currentPath = window.location.pathname;
  document.querySelectorAll('.nav-item').forEach(function (item) {
    var href = item.getAttribute('href') || '';
    if (href && currentPath.indexOf(href) === 0) {
      item.classList.add('active');
    }
  });

  /* ── Dropdown Menus ───────────────────────────────────────────────────── */
  function setupDropdown(btnId, panelId) {
    var btn   = document.getElementById(btnId);
    var panel = document.getElementById(panelId);
    if (!btn || !panel) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      var isHidden = panel.hidden;
      // Close all dropdowns first
      document.querySelectorAll('.dropdown-panel').forEach(function (p) { p.hidden = true; });
      panel.hidden = !isHidden;
    });

    document.addEventListener('click', function () {
      panel.hidden = true;
    });
    panel.addEventListener('click', function (e) { e.stopPropagation(); });
  }

  setupDropdown('notif-btn',      'notif-panel');
  setupDropdown('user-menu-btn',  'user-panel');

  /* ── Flash Auto-dismiss ───────────────────────────────────────────────── */
  document.querySelectorAll('.flash').forEach(function (flash) {
    var delay = parseInt(flash.getAttribute('data-auto-dismiss') || '5000', 10);

    // Close button
    var closeBtn = flash.querySelector('.flash-close');
    if (closeBtn) {
      closeBtn.addEventListener('click', function () { dismissFlash(flash); });
    }

    // Auto-dismiss
    setTimeout(function () { dismissFlash(flash); }, delay);
  });

  function dismissFlash(el) {
    el.style.opacity = '0';
    el.style.transition = 'opacity 0.3s ease';
    setTimeout(function () { if (el.parentNode) el.parentNode.removeChild(el); }, 300);
  }

  /* ── Helper: Escape HTML ─────────────────────────────────────────────── */
  function escHtml(str) {
    if (!str) return '';
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  /* ── Notification Polling & Real-time Management ─────────────────────── */
  var notifCount   = document.getElementById('notif-count');
  var notifList    = document.getElementById('notif-list');
  var notifBtn     = document.getElementById('notif-btn');
  var notifMarkAll = document.getElementById('notif-mark-all-btn');

  function getNotifApiUrl() {
    var base = (window.RMS && window.RMS.baseUrl) ? window.RMS.baseUrl.replace(/\/+$/, '') : '';
    return base + '/admin/api/notifications';
  }

  function loadNotifications() {
    if (!notifCount || !notifList) return;
    rmsGet(getNotifApiUrl())
      .then(function (res) {
        if (!res || res.status !== 'success') return;
        var items = res.data || [];
        var unread = items.filter(function (n) { return n.is_unread || !n.read_at; });

        if (unread.length > 0) {
          notifCount.hidden = false;
          notifCount.textContent = unread.length > 99 ? '99+' : unread.length;
        } else {
          notifCount.hidden = true;
        }

        if (typeof res.live_orders_count !== 'undefined') {
          var liveBadge = document.getElementById('live-orders-count');
          if (liveBadge) {
            var cnt = parseInt(res.live_orders_count, 10) || 0;
            liveBadge.textContent = cnt;
            liveBadge.style.display = cnt > 0 ? 'inline-block' : 'none';
          }
        }

        if (items.length === 0) {
          notifList.innerHTML = '<p class="panel-empty" style="padding: 1.5rem 1rem; text-align: center; color: var(--text-muted); font-size: .85rem;">No new notifications</p>';
        } else {
          notifList.innerHTML = items.slice(0, 10).map(function (n) {
            return '<div class="notif-item' + (n.is_unread ? ' unread' : '') + '" data-id="' + n.id + '" onclick="window.markNotifRead(' + n.id + ')">' +
              '<div class="notif-title">' + escHtml(n.title) + '</div>' +
              '<div class="notif-body text-small text-muted">' + escHtml(n.body || '') + '</div>' +
              '<div class="notif-time text-xs text-muted">' + escHtml(n.created_at) + '</div>' +
              '</div>';
          }).join('');
        }
      })
      .catch(function (err) {
        console.warn('Notification poll issue:', err);
      });
  }

  window.markNotifRead = function(id) {
    rmsPost(getNotifApiUrl() + '/read', { id: id })
      .then(function() {
        loadNotifications();
      });
  };

  if (notifMarkAll) {
    notifMarkAll.addEventListener('click', function(e) {
      e.stopPropagation();
      rmsPost(getNotifApiUrl() + '/read', { all: true })
        .then(function() {
          loadNotifications();
        });
    });
  }

  if (notifBtn) {
    notifBtn.addEventListener('click', function() {
      loadNotifications();
    });
  }

  if (notifCount || notifList) {
    loadNotifications();
    setInterval(loadNotifications, 15000); // Poll every 15 seconds
  }

  /* ── Confirm dialogs ──────────────────────────────────────────────────── */
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-confirm]');
    if (!el) return;
    var msg = el.getAttribute('data-confirm') || 'Are you sure?';
    if (!window.confirm(msg)) {
      e.preventDefault();
      e.stopPropagation();
    }
  });

  /* ── Toggle switches (active/inactive via AJAX) ───────────────────────── */
  document.addEventListener('click', function (e) {
    var el = e.target.closest('[data-toggle-url]');
    if (!el) return;
    e.preventDefault();

    var url    = el.getAttribute('data-toggle-url');
    var target = el.getAttribute('data-toggle-target');

    rmsPost(url, {})
      .then(function (res) {
        if (res.status === 'success') {
          if (target) {
            var badge = document.querySelector('[data-status-id="' + target + '"]');
            if (badge) {
              badge.classList.toggle('status-active');
              badge.classList.toggle('status-inactive');
              badge.textContent = badge.classList.contains('status-active') ? 'Active' : 'Inactive';
            }
          }
          showFlash('success', res.message || 'Status updated.');
        } else {
          showFlash('error', res.message || 'An error occurred.');
        }
      })
      .catch(function () { showFlash('error', 'Network error. Please try again.'); });
  });

  /* ── Dynamic Flash ────────────────────────────────────────────────────── */
  window.showFlash = function (type, message) {
    var container = document.getElementById('flash-container');
    if (!container) return;

    var div = document.createElement('div');
    div.className = 'flash flash-' + type + ' animate-fade';
    div.setAttribute('role', 'alert');
    div.innerHTML = '<span>' + (type === 'success' ? '&#10003;' : '&#9888;') + '</span> ' +
      escHtml(message) +
      '<button class="flash-close" aria-label="Dismiss">&times;</button>';

    div.querySelector('.flash-close').addEventListener('click', function () { dismissFlash(div); });
    container.appendChild(div);
    setTimeout(function () { dismissFlash(div); }, type === 'error' ? 8000 : 5000);
  };

  /* ── Attendance & Quick Clock-In / Clock-Out Station ───────────────────── */
  window.currentAttendanceStatus = null;

  window.openQuickClockModal = function () {
    var modal = document.getElementById('quick-clock-modal');
    if (modal) {
      modal.removeAttribute('hidden');
      modal.style.setProperty('display', 'flex', 'important');
      window.loadAttendanceStatus();
    }
  };

  window.closeQuickClockModal = function () {
    var modal = document.getElementById('quick-clock-modal');
    if (modal) {
      modal.style.setProperty('display', 'none', 'important');
      modal.setAttribute('hidden', '');
    }
  };

  window.loadAttendanceStatus = function () {
    if (!window.RMS || !window.RMS.baseUrl || !window.RMS.userId) return;
    
    var url = window.RMS.baseUrl + 'admin/attendance/my-status';
    fetch(url, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(function (r) { return r.json(); })
    .then(function (res) {
      if (res && res.status === 'success' && res.data) {
        window.currentAttendanceStatus = res.data;
        updateAttendanceStationUI(res.data);
      }
    })
    .catch(function (err) {
      console.warn('Unable to load attendance status:', err);
    });
  };

  function updateAttendanceStationUI(data) {
    // 1. Topbar Elements
    var dot = document.getElementById('topbar-clock-dot');
    var btnText = document.getElementById('topbar-clock-text');
    var topbarBtn = document.getElementById('btn-topbar-clock');

    if (dot && btnText) {
      if (data.is_on_duty) {
        dot.style.background = '#10b981'; // Green
        dot.style.boxShadow = '0 0 8px rgba(16, 185, 129, 0.9)';
        btnText.textContent = 'On Duty (' + (data.check_in_time || '') + ')';
        if (topbarBtn) {
          topbarBtn.style.borderColor = 'rgba(16, 185, 129, 0.45)';
          topbarBtn.style.color = '#10b981';
          topbarBtn.style.background = 'rgba(16, 185, 129, 0.1)';
        }
      } else if (data.is_checked_out) {
        dot.style.background = '#64748b'; // Slate
        dot.style.boxShadow = 'none';
        btnText.textContent = 'Clocked Out';
        if (topbarBtn) {
          topbarBtn.style.borderColor = 'rgba(255, 255, 255, 0.12)';
          topbarBtn.style.color = 'var(--text-secondary, #94a3b8)';
          topbarBtn.style.background = 'var(--surface-raised, rgba(255,255,255,0.03))';
        }
      } else {
        // Not yet clocked in today
        dot.style.background = '#f59e0b'; // Amber
        dot.style.boxShadow = '0 0 8px rgba(245, 158, 11, 0.8)';
        btnText.textContent = 'Clock In';
        if (topbarBtn) {
          topbarBtn.style.borderColor = 'rgba(245, 158, 11, 0.4)';
          topbarBtn.style.color = '#f59e0b';
          topbarBtn.style.background = 'rgba(245, 158, 11, 0.08)';
        }
      }
    }

    // 2. Modal Elements
    var shiftEl = document.getElementById('modal-shift-name');
    var statusBadge = document.getElementById('modal-clock-status-badge');
    var actionContainer = document.getElementById('modal-clock-action-container');

    if (shiftEl) {
      if (data.assigned_shift && data.assigned_shift.shift_name) {
        var s = data.assigned_shift;
        var startFormatted = s.start_time ? s.start_time.substring(0, 5) : '';
        var endFormatted = s.end_time ? s.end_time.substring(0, 5) : '';
        shiftEl.innerHTML = escHtml(s.shift_name) + ' <span style="font-weight: 400; color: var(--text-secondary);">(' + startFormatted + ' - ' + endFormatted + ')</span>';
      } else {
        shiftEl.textContent = 'General / Unscheduled Shift';
      }
    }

    if (statusBadge) {
      if (data.is_on_duty) {
        statusBadge.className = 'badge badge-success';
        statusBadge.textContent = '🟢 On Duty (Checked in at ' + (data.check_in_time || '') + ')';
      } else if (data.is_checked_out) {
        statusBadge.className = 'badge badge-secondary';
        statusBadge.textContent = '✔️ Shift Finished (Left at ' + (data.check_out_time || '') + ')';
      } else {
        statusBadge.className = 'badge badge-warning';
        statusBadge.textContent = '⚠️ Not Clocked In Yet';
      }
    }

    if (actionContainer) {
      if (data.is_on_duty) {
        actionContainer.innerHTML =
          '<button type="button" class="btn btn-danger" id="btn-modal-clock-action" onclick="doQuickClockOut()" style="width: 100%; padding: 0.8rem; font-size: 1rem; font-weight: 700; box-shadow: 0 4px 14px rgba(239, 68, 68, 0.45); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">' +
          '<span>🔴</span> Clock Out (End Duty)' +
          '</button>';
      } else if (data.is_checked_out) {
        actionContainer.innerHTML =
          '<div style="background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.35); border-radius: 8px; padding: 0.85rem; color: #10b981; font-weight: 600; text-align: center;">' +
          '✔️ Attendance logged for today. Shift complete (' + (data.total_hours || '0') + 'h logged).' +
          '</div>';
      } else {
        actionContainer.innerHTML =
          '<button type="button" class="btn btn-success" id="btn-modal-clock-action" onclick="doQuickClockIn()" style="width: 100%; padding: 0.8rem; font-size: 1rem; font-weight: 700; box-shadow: 0 4px 14px rgba(16, 185, 129, 0.45); cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;">' +
          '<span>⏱️</span> Clock In Now' +
          '</button>';
      }
    }
  }

  window.doQuickClockIn = function (shiftId) {
    var btn = document.getElementById('btn-modal-clock-action');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = 'Clocking In...';
    }

    var fd = new FormData();
    fd.append(window.RMS.csrfName, window.RMS.csrfHash);
    fd.append('user_id', window.RMS.userId);
    if (shiftId) {
      fd.append('shift_id', shiftId);
    } else if (window.currentAttendanceStatus && window.currentAttendanceStatus.assigned_shift && window.currentAttendanceStatus.assigned_shift.shift_id) {
      fd.append('shift_id', window.currentAttendanceStatus.assigned_shift.shift_id);
    }

    fetch(window.RMS.baseUrl + 'admin/attendance/check-in', {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(function (r) {
      var newHash = r.headers.get('X-CSRF-Token');
      if (newHash && window.RMS) window.RMS.csrfHash = newHash;
      return r.json();
    })
    .then(function (res) {
      if (res.status === 'success') {
        if (window.showFlash) showFlash('success', res.message || 'Clocked in successfully!');
        window.loadAttendanceStatus();
        setTimeout(function () {
          window.closeQuickClockModal();
          if (window.location.pathname.indexOf('/attendance') !== -1) {
            window.location.reload();
          }
        }, 600);
      } else {
        if (window.showFlash) showFlash('error', res.message || 'Check-in failed');
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<span>⏱️</span> Clock In Now';
        }
      }
    })
    .catch(function (err) {
      if (window.showFlash) showFlash('error', 'Network error while clocking in');
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<span>⏱️</span> Clock In Now';
      }
    });
  };

  window.doQuickClockOut = function () {
    if (!confirm('Are you sure you want to clock out for today?')) return;

    var btn = document.getElementById('btn-modal-clock-action');
    if (btn) {
      btn.disabled = true;
      btn.innerHTML = 'Clocking Out...';
    }

    var fd = new FormData();
    fd.append(window.RMS.csrfName, window.RMS.csrfHash);

    fetch(window.RMS.baseUrl + 'admin/attendance/clock-out-self', {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest'
      }
    })
    .then(function (r) {
      var newHash = r.headers.get('X-CSRF-Token');
      if (newHash && window.RMS) window.RMS.csrfHash = newHash;
      return r.json();
    })
    .then(function (res) {
      if (res.status === 'success') {
        if (window.showFlash) showFlash('success', res.message || 'Clocked out successfully!');
        window.loadAttendanceStatus();
        setTimeout(function () {
          window.closeQuickClockModal();
          if (window.location.pathname.indexOf('/attendance') !== -1) {
            window.location.reload();
          }
        }, 600);
      } else {
        if (window.showFlash) showFlash('error', res.message || 'Check-out failed');
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = '<span>🔴</span> Clock Out (End Duty)';
        }
      }
    })
    .catch(function (err) {
      if (window.showFlash) showFlash('error', 'Network error while clocking out');
      if (btn) {
        btn.disabled = false;
        btn.innerHTML = '<span>🔴</span> Clock Out (End Duty)';
      }
    });
  };

  // Initialize status on page load
  if (window.RMS && window.RMS.userId) {
    window.loadAttendanceStatus();
    setInterval(window.loadAttendanceStatus, 60000); // 1 minute auto refresh
  }

  /* ── Utility ──────────────────────────────────────────────────────────── */
  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

})();
