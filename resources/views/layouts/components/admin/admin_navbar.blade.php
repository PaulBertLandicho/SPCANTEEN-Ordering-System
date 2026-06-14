<div class="admin-navbar">
  <div class="column">
    <div class="logo">
      <img id="logo" src="/images/SPCanteen.png" alt="SPCanteen.png">
    </div>
    <div class="icon-bar">
      <div class="nav-btns">
        <a id="admin1" href="administrator">
          <iconify-icon icon="clarity:dashboard-line" style="font-size: 26px;"></iconify-icon>
          <span id="nav-txt">Dashboard</span>
        </a>
      </div>
      <div class="nav-btns">
        <a id="admin2" href="product_list">
          <iconify-icon icon="el:list-alt" style="font-size: 26px;"></iconify-icon>
          <span id="nav-txt">Product List</span>
        </a>
      </div>
      <div class="nav-btns">
        <a id="admin3" href="order_list">
          <iconify-icon icon="ic:baseline-pending-actions" style="font-size: 26px;"></iconify-icon>
          <span id="nav-txt">Order List</span>
        </a>
      </div>
      <div class="nav-btns">
        <a id="admin4" href="transaction_history">
          <iconify-icon icon="fluent:clipboard-task-list-16-regular" style="font-size: 26px;"></iconify-icon>
          <span id="nav-txt">Transaction History</span>
        </a>
      </div>
      @if(Auth::user()->role_id == 4)
      <div class="nav-btns">
        <a id="admin5" href="manage_user">
          <iconify-icon icon="fluent:notepad-person-24-regular" style="font-size: 26px;"></iconify-icon>
          <span id="nav-txt">Manage Users</span>
        </a>
      </div>
      @endif
      <form action="/logout" method="POST">
        @csrf
        <div class="nav-btns">
          <button id="logout" type="submit">
            <a>
              <iconify-icon icon="humbleicons:logout" style="font-size: 28px;"></iconify-icon>
              <span id="nav-txt">Logout</span>
            </a>
          </button>
        </div>
      </form>
      <div class="copyright">
        <p style="color: #999; font-size:13px; margin-top:45px;">
          <b>SPCANTEEN</b>
          <br>© 2024 All Rights Reserved
        </p>
      </div>
    </div>
  </div>
</div>
<script>
  // background-color: maroon;
  // color: white;
  // border-radius: 10px;
  const navContainer = document.querySelector('.admin-navbar');
  const navs = document.querySelectorAll('.icon-bar .nav-btns a');
  let temp = "";

  (function() {
    const path = window.location.pathname.replace(/^\//, '').split('/')[0];
    const mapping = {
      'administrator': 'admin1',
      'product_list': 'admin2',
      'order_list': 'admin3',
      'transaction_history': 'admin4',
      'manage_user': 'admin5'
    };

    // clear existing active styling
    document.querySelectorAll('.icon-bar .nav-btns a').forEach(a => {
      a.style.backgroundColor = '';
      a.style.color = '';
      a.style.borderRadius = '';
      a.style.cursor = 'pointer';
    });

    const activeId = mapping[path];
    if (activeId) {
      const el = document.getElementById(activeId);
      if (el) {
        el.style.backgroundColor = 'maroon';
        el.style.color = 'white';
        el.style.borderRadius = '10px';
        el.style.cursor = 'default';
        temp = activeId;
      }
    }
  })();

  navContainer.addEventListener('mouseenter', function() {
    navs.forEach((nav) => {
      nav.addEventListener('mouseenter', function() {
        document.getElementById(temp).style.backgroundColor = "maroon";
        document.getElementById(temp).style.color = "white";
        document.getElementById(temp).style.borderRadius = "";
        document.getElementById(temp).style.cursor = "default";
        if (nav.id != temp) {
          document.getElementById(temp).style.backgroundColor = "white";
          document.getElementById(temp).style.color = "black";
          document.getElementById(temp).style.borderRadius = "";
          document.getElementById(temp).style.cursor = "default";

          document.getElementById(nav.id).style.backgroundColor = "maroon";
          document.getElementById(nav.id).style.color = "white";
          document.getElementById(nav.id).style.borderRadius = "";
          document.getElementById(nav.id).style.cursor = "pointer";

          nav.addEventListener('mouseleave', function() {
            document.getElementById(nav.id).style.backgroundColor = "white";
            document.getElementById(nav.id).style.color = "black";
            document.getElementById(nav.id).style.borderRadius = "";
            document.getElementById(nav.id).style.cursor = "pointer";
          });
        }
      });
    });
  });


  navContainer.addEventListener('mouseleave', function() {
    // restore active nav styling (temp)
    navs.forEach(n => {
      n.style.backgroundColor = 'white';
      n.style.color = 'black';
      n.style.borderRadius = '';
      n.style.cursor = 'pointer';
    });
    if (temp) {
      const el = document.getElementById(temp);
      if (el) {
        el.style.backgroundColor = 'maroon';
        el.style.color = 'white';
        el.style.borderRadius = '10px';
        el.style.cursor = 'default';
      }
    }
  });
</script>