<!-- Top Nav Bar (Desktop) -->
<header class="biz-topnav">
  
  <div class="biz-topnav-left">
    <div class="biz-page-header">
      <h1 class="biz-page-title" id="biz-page-title">Dashboard</h1>
    </div>
  </div>

  <div class="biz-topnav-center">
    <!-- Floating Command Palette trigger -->
    <button class="biz-cmd-trigger" onclick="bizOpenCommandPalette()">
        <i class="fas fa-search"></i>
        <span>Search actions, products...</span>
        <kbd>Ctrl K</kbd>
    </button>
  </div>

  <div class="biz-nav-right">
    <!-- Quick Actions (Premium micro-interaction: Quick add from header) -->
    <button class="biz-btn biz-btn-primary biz-btn-sm biz-hidden-mobile" style="font-weight:600" onclick="bizOpenModal('biz-modal-quick-sale')">
      <i class="fas fa-plus"></i> Add Sale
    </button>

    <button class="biz-icon-btn" title="Tema" onclick="bizToggleTheme()">
      <i class="fas fa-circle-half-stroke"></i>
    </button>
    <button class="biz-icon-btn" title="Menu" onclick="bizToggleNavMenu('biz-topnav-menu')">
      <i class="fas fa-ellipsis"></i>
    </button>

    <!-- Menu Dropdown -->
    <div class="biz-nav-dropdown" id="biz-topnav-menu">
      <button class="biz-nav-dd-btn" onclick="bizOpenModal('biz-modal-backup');bizCloseNavMenus()">
        <i class="fas fa-cloud-arrow-down"></i> Backup Data
      </button>
      <button class="biz-nav-dd-btn" onclick="bizOpenModal('biz-modal-business');bizCloseNavMenus()">
        <i class="fas fa-store"></i> Info Bisnis
      </button>
      <a href="/cashbook" class="biz-nav-dd-btn" style="text-decoration:none">
        <i class="fas fa-arrow-left"></i> Personal Cashbook
      </a>
    </div>
  </div>

</header>
