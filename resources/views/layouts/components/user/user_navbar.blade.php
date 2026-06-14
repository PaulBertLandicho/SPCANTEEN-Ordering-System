<div class="user-navbar">
      <div class="row">
            <a href="/" id="user-menu-nav">
                  <iconify-icon id="menu-icons" icon="ep:menu"></iconify-icon>
            </a>
            <span id="menu-txt">Menu</span>
      </div>
      <div class="row">
            <a href="favorite" id="user-favorite-nav">
                  <iconify-icon id="favorite-icons" icon="material-symbols:favorite"></iconify-icon>
            </a>
            <span id="favor-txt">Favorite</span>
      </div>
      <div class="row">
            <a href="history" id="user-history-nav">
                  <iconify-icon id="history-icons" icon="bi:clock-history"></iconify-icon>
            </a>
            <span id="his-txt">History</span>
      </div>
      <div class="row">
            <a href="profile" id="user-profile-nav">
                  <iconify-icon id="profile-icons" icon="gg:profile"></iconify-icon>
            </a>
            <span id="prof-txt">Profile</span>
      </div>
</div>

<script>
      if (currentUrl == 'http://127.0.0.1:8000/') {
            document.getElementById('menu-icons').style.color = "#00FF7F";
            document.getElementById('menu-txt').style.color = "#00FF7F";
            document.getElementById('user-menu-nav').style.cursor = "default";
      } else if (currentUrl == 'http://127.0.0.1:8000/favorite') {
            document.getElementById('favorite-icons').style.color = "#00FF7F";
            document.getElementById('favor-txt').style.color = "#00FF7F";
            document.getElementById('user-favorite-nav').style.cursor = "default";
      } else if (currentUrl == 'http://127.0.0.1:8000/history') {
            document.getElementById('history-icons').style.color = "#00FF7F";
            document.getElementById('his-txt').style.color = "#00FF7F";
            document.getElementById('user-history-nav').style.cursor = "default";
      } else if (currentUrl == 'http://127.0.0.1:8000/profile') {
            document.getElementById('profile-icons').style.color = "#00FF7F";
            document.getElementById('prof-txt').style.color = "#00FF7F";
            document.getElementById('user-profile-nav').style.cursor = "default";
      }
</script>