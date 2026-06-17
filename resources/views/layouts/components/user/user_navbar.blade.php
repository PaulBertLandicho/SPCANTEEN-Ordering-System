<div class="user-navbar">
      <div class="row {{ View::yieldContent('page') == 'menu' ? 'active' : '' }}">
            <a href="/" id="user-menu-nav">
                  <iconify-icon id="nav-icons" icon="mdi:food"></iconify-icon>
            </a>
            <span id="nav-txt">Menu</span>
      </div>

      <div class="row {{ View::yieldContent('page') == 'favorite' ? 'active' : '' }}">
            <a href="favorite" id="user-favorite-nav">
                  <iconify-icon id="nav-icons" icon="material-symbols:favorite"></iconify-icon>
            </a>
            <span id="nav-txt">Favorite</span>
      </div>
      <div class="row {{ View::yieldContent('page') == 'history' ? 'active' : '' }}">
            <a href="history" id="user-history-nav">
                  <iconify-icon id="nav-icons" icon="bi:clock-history"></iconify-icon>
            </a>
            <span id="nav-txt">History</span>
      </div>
      <div class="row {{ View::yieldContent('page') == 'profile' ? 'active' : '' }}">
            <a href="profile" id="user-profile-nav">
                  <iconify-icon id="nav-icons" icon="gg:profile"></iconify-icon>
            </a>
            <span id="nav-txt">Profile</span>
      </div>
</div>