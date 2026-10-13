<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>首頁 — 品牌名稱</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+TC:wght@300;400;500;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <!-- 全站唯一共用 CSS -->
  <link rel="stylesheet" href="css/style.css" />
</head>
<body class="page-home">
  <!-- ===== Topbar ===== -->
  <div id="topbar">
    <a href="member.html"><i class="fa fa-user-circle"></i> 會員中心</a>
    <span class="sep">|</span>
    <a href="login.html"><i class="fa fa-sign-in-alt"></i> 登入</a>
    <span class="sep">|</span>
    <a href="register.html"><i class="fa fa-user-plus"></i> 加入會員</a>
    <span class="sep">|</span>
    <a href="login.html" onclick="return confirm('確定要登出？')"><i class="fa fa-sign-out-alt"></i> 登出</a>
  </div>

  <!-- ===== Navbar ===== -->
  <nav id="navbar">
    <a href="index.html" class="nav-logo">
      <div class="logo-box">B</div>
      <span class="logo-text">品牌名稱</span>
    </a>
    <ul class="nav-links">
      <li>
        <a href="news-list.html"><i class="fa fa-newspaper"></i> 最新消息</a>
        <ul class="dropdown">
          <li><a href="news-list.html?cat=1">公司公告</a></li>
          <li><a href="news-list.html?cat=2">活動資訊</a></li>
          <li><a href="news-list.html?cat=3">產品動態</a></li>
        </ul>
      </li>
      <li>
        <a href="products-list.html"><i class="fa fa-box-open"></i> 產品介紹</a>
        <ul class="dropdown">
          <li><a href="products-list.html?cat=1">系列一</a></li>
          <li><a href="products-list.html?cat=2">系列二</a></li>
          <li><a href="products-list.html?cat=3">系列三</a></li>
        </ul>
      </li>
      <li><a href="milestone.html"><i class="fa fa-history"></i> 大事記</a></li>
      <li><a href="about.html"><i class="fa fa-building"></i> 關於我們</a></li>
      <li><a href="member.html"><i class="fa fa-user"></i> 會員中心</a></li>
    </ul>
    <a href="cart.html" class="nav-cart">
      <i class="fa fa-shopping-cart"></i>
      <span>購物車</span>
      <span class="cart-badge" id="cart-badge">3</span>
    </a>
    <button class="nav-toggle" onclick="toggleNav()" aria-label="選單"><i class="fa fa-bars"></i></button>
  </nav>

  <!-- ===== Main Content ===== -->
  <main id="content-wrap">
    <!-- ===== Hero 輪播 ===== -->
    <div class="hero" role="region" aria-label="首頁輪播">
      <button class="hero-prev" onclick="heroMove(-1)" aria-label="上一張"><i class="fa fa-chevron-left"></i></button>
      <button class="hero-next" onclick="heroMove(1)" aria-label="下一張"><i class="fa fa-chevron-right"></i></button>

      <div class="hero-slides" id="heroSlides">
        <div class="hero-slide bg-1">
          <div class="hero-overlay"></div>
          <div class="hero-content">
            <div class="hero-eyebrow">全新系列 2025</div>
            <h1 class="hero-title">卓越品質<br>成就每一刻</h1>
            <p class="hero-desc">嚴選頂級原料，結合精湛工藝，為您帶來無與倫比的使用體驗。</p>
            <div class="hero-actions">
              <a href="products-list.html" class="btn btn-hero"><i class="fa fa-shopping-bag"></i> 立即選購</a>
              <a href="about.html" class="btn btn-hero-outline">了解更多</a>
            </div>
          </div>
        </div>
        <div class="hero-slide bg-2">
          <div class="hero-overlay"></div>
          <div class="hero-content">
            <div class="hero-eyebrow">限時優惠</div>
            <h1 class="hero-title">夏季特惠<br>全館最高 <em>8 折</em></h1>
            <p class="hero-desc">活動期間限定，多項人氣商品同步特價，把握機會立即搶購。</p>
            <div class="hero-actions">
              <a href="products-list.html" class="btn btn-hero"><i class="fa fa-tags"></i> 查看優惠商品</a>
            </div>
          </div>
        </div>
        <div class="hero-slide bg-3">
          <div class="hero-overlay"></div>
          <div class="hero-content">
            <div class="hero-eyebrow">會員獨享</div>
            <h1 class="hero-title">加入會員<br>享更多專屬好禮</h1>
            <p class="hero-desc">立即免費加入，獲得首購折扣、積點回饋與會員專屬活動通知。</p>
            <div class="hero-actions">
              <a href="register.html" class="btn btn-hero"><i class="fa fa-user-plus"></i> 免費加入</a>
              <a href="login.html" class="btn btn-hero-outline">已是會員？登入</a>
            </div>
          </div>
        </div>
      </div>

      <div class="hero-controls" id="heroDots">
        <button class="hero-dot active" onclick="heroGo(0)" aria-label="第 1 張"></button>
        <button class="hero-dot" onclick="heroGo(1)" aria-label="第 2 張"></button>
        <button class="hero-dot" onclick="heroGo(2)" aria-label="第 3 張"></button>
      </div>
    </div>

    <!-- ===== 品牌數字 ===== -->
    <div class="stats-band">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item reveal"><div class="stat-num" data-target="12" data-suffix="+">0</div><div class="stat-label">品牌成立年數</div></div>
          <div class="stat-item reveal"><div class="stat-num" data-target="280" data-suffix="+">0</div><div class="stat-label">產品品項</div></div>
          <div class="stat-item reveal"><div class="stat-num" data-target="50000" data-suffix="+">0</div><div class="stat-label">累計服務會員</div></div>
          <div class="stat-item reveal"><div class="stat-num" data-target="98" data-suffix="%">0</div><div class="stat-label">顧客好評率</div></div>
        </div>
      </div>
    </div>

    <!-- ===== 核心優勢 ===== -->
    <section class="section-lg section-alt">
      <div class="container">
        <div class="section-header reveal">
          <div class="section-eyebrow">Why Us</div>
          <h2 class="section-title">我們的<span>核心優勢</span></h2>
          <div class="gold-line"></div>
          <p class="section-desc">從產品開發到售後服務，每一個環節都傾注我們對品質的執著。</p>
        </div>
        <div class="features-grid">
          <div class="feature-card reveal">
            <div class="feature-icon"><i class="fa fa-medal"></i></div>
            <div class="feature-title">嚴格品質把關</div>
            <p class="feature-desc">每件產品出廠前均通過多道檢測程序，確保每一次購買都令您滿意。</p>
          </div>
          <div class="feature-card reveal">
            <div class="feature-icon"><i class="fa fa-truck"></i></div>
            <div class="feature-title">快速安全配送</div>
            <p class="feature-desc">與台灣主要物流合作，訂單成立後 24 小時內出貨，全程追蹤貨態。</p>
          </div>
          <div class="feature-card reveal">
            <div class="feature-icon"><i class="fa fa-headset"></i></div>
            <div class="feature-title">專業客服支援</div>
            <p class="feature-desc">週一至週六 09:00–18:00 專人服務，LINE 官方帳號隨時為您解答。</p>
          </div>
        </div>
      </div>
    </section>

    <!-- ===== 精選產品 ===== -->
    <section class="section-lg">
      <div class="container">
        <div class="section-header reveal">
          <div class="section-eyebrow">Featured Products</div>
          <h2 class="section-title">精選<span>熱銷商品</span></h2>
          <div class="gold-line"></div>
        </div>
        <div class="product-grid">
          <div class="product-card reveal">
            <a href="product-detail.html" class="product-img ph"><i class="fa fa-cube"></i><span class="product-badge gold">NEW</span></a>
            <div class="product-info">
              <div class="product-cat">系列一</div>
              <a href="product-detail.html" class="product-title">精品系列旗艦款 A1</a>
              <div class="product-price"><span class="price-now">NT$ 2,980</span><span class="price-was">NT$ 3,500</span></div>
            </div>
            <button class="btn-cart" onclick="addToCart(this)"><i class="fa fa-cart-plus"></i> 加入購物車</button>
          </div>
          <div class="product-card reveal">
            <a href="product-detail.html" class="product-img ph"><i class="fa fa-cube"></i></a>
            <div class="product-info">
              <div class="product-cat">系列二</div>
              <a href="product-detail.html" class="product-title">經典系列標準款 B2</a>
              <div class="product-price"><span class="price-now">NT$ 1,680</span></div>
            </div>
            <button class="btn-cart" onclick="addToCart(this)"><i class="fa fa-cart-plus"></i> 加入購物車</button>
          </div>
          <div class="product-card reveal">
            <a href="product-detail.html" class="product-img ph"><i class="fa fa-cube"></i><span class="product-badge">HOT</span></a>
            <div class="product-info">
              <div class="product-cat">系列三</div>
              <a href="product-detail.html" class="product-title">限量聯名款特別版 C1</a>
              <div class="product-price"><span class="price-now">NT$ 4,200</span><span class="price-was">NT$ 5,000</span></div>
            </div>
            <button class="btn-cart" onclick="addToCart(this)"><i class="fa fa-cart-plus"></i> 加入購物車</button>
          </div>
          <div class="product-card reveal">
            <a href="product-detail.html" class="product-img ph"><i class="fa fa-cube"></i></a>
            <div class="product-info">
              <div class="product-cat">系列一</div>
              <a href="product-detail.html" class="product-title">精品系列進階款 A2</a>
              <div class="product-price"><span class="price-now">NT$ 3,480</span><span class="price-was">NT$ 3,900</span></div>
            </div>
            <button class="btn-cart" onclick="addToCart(this)"><i class="fa fa-cart-plus"></i> 加入購物車</button>
          </div>
        </div>
        <div class="section-footer reveal">
          <a href="products-list.html" class="btn btn-primary btn-more">查看全部商品 <i class="fa fa-arrow-right"></i></a>
        </div>
      </div>
    </section>

    <!-- ===== 最新消息 ===== -->
    <section class="section-lg section-alt">
      <div class="container">
        <div class="section-header reveal">
          <div class="section-eyebrow">Latest News</div>
          <h2 class="section-title">最新<span>消息</span></h2>
          <div class="gold-line"></div>
        </div>
        <div class="news-grid">
          <a href="news-detail.html" class="news-card reveal">
            <div class="news-cover ph"><i class="fa fa-newspaper"></i></div>
            <div class="news-card-body">
              <div class="news-meta"><span class="news-date">2025-06-01</span><span class="news-badge">公司公告</span></div>
              <div class="news-card-title">2025 年下半年新品發布計畫正式公告</div>
              <div class="news-card-excerpt">全新系列將於第三季陸續上市，涵蓋精品旗艦款與入門款，歡迎各位顧客持續關注。</div>
              <span class="news-read-more">閱讀更多 <i class="fa fa-arrow-right"></i></span>
            </div>
          </a>
          <a href="news-detail.html" class="news-card reveal">
            <div class="news-cover ph"><i class="fa fa-newspaper"></i></div>
            <div class="news-card-body">
              <div class="news-meta"><span class="news-date">2025-05-20</span><span class="news-badge cat-2">活動資訊</span></div>
              <div class="news-card-title">夏季會員回饋活動開跑，限時消費享雙倍積點</div>
              <div class="news-card-excerpt">即日起至 6 月 30 日，會員消費每滿 NT$1,000 即享雙倍點數回饋，點數可折抵下次購物。</div>
              <span class="news-read-more">閱讀更多 <i class="fa fa-arrow-right"></i></span>
            </div>
          </a>
          <a href="news-detail.html" class="news-card reveal">
            <div class="news-cover ph"><i class="fa fa-newspaper"></i></div>
            <div class="news-card-body">
              <div class="news-meta"><span class="news-date">2025-05-10</span><span class="news-badge cat-3">產品動態</span></div>
              <div class="news-card-title">C 系列限量聯名款正式開放預購</div>
              <div class="news-card-excerpt">與知名設計師跨界合作的 C1 聯名款現正接受預購，數量有限，先搶先贏。</div>
              <span class="news-read-more">閱讀更多 <i class="fa fa-arrow-right"></i></span>
            </div>
          </a>
        </div>
        <div class="section-footer reveal">
          <a href="news-list.html" class="btn btn-primary btn-more">查看全部消息 <i class="fa fa-arrow-right"></i></a>
        </div>
      </div>
    </section>

    <!-- ===== CTA ===== -->
    <div class="cta-band">
      <div class="container reveal">
        <div class="cta-eyebrow">加入我們</div>
        <h2 class="cta-title">立即加入會員<br>享受更多專屬優惠</h2>
        <p class="cta-desc">免費註冊，首購享 9 折優惠，並獲得積點回饋與不定期會員專屬活動。</p>
        <div class="cta-actions">
          <a href="register.html" class="btn btn-hero"><i class="fa fa-user-plus"></i> 立即免費加入</a>
          <a href="products-list.html" class="btn btn-hero-outline"><i class="fa fa-box-open"></i> 瀏覽商品</a>
        </div>
      </div>
    </div>

  </main>

  <!-- ===== Footer ===== -->
  <footer class="site-footer">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo-box">B</div>
        <p>提供優質產品與服務<br>您的信賴之選</p>
        <div class="footer-social">
          <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
          <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
          <a href="#" aria-label="LINE"><i class="fab fa-line"></i></a>
        </div>
      </div>
      <div class="footer-col">
        <h4>快速連結</h4>
        <ul>
          <li><a href="about.html">關於我們</a></li>
          <li><a href="news-list.html">最新消息</a></li>
          <li><a href="products-list.html">產品介紹</a></li>
          <li><a href="milestone.html">大事記</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>會員服務</h4>
        <ul>
          <li><a href="login.html">會員登入</a></li>
          <li><a href="register.html">加入會員</a></li>
          <li><a href="member.html">會員中心</a></li>
          <li><a href="cart.html">購物車</a></li>
        </ul>
      </div>
      <div class="footer-col contact">
        <h4>聯絡資訊</h4>
        <ul>
          <li><i class="fa fa-map-marker-alt"></i> 台灣某市某區某路一號</li>
          <li><i class="fa fa-phone"></i> 02-1234-5678</li>
          <li><i class="fa fa-envelope"></i> info@example.com</li>
          <li><i class="fa fa-clock"></i> 週一至週五 09:00–18:00</li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>&copy; 2025 品牌名稱. All rights reserved.</span>
      <span>隱私權政策 | 服務條款</span>
    </div>
  </footer>

  <button id="back-top" aria-label="回頂端"><i class="fa fa-chevron-up"></i></button>
  <script src="js/main.js"></script>
  <script>

    /* Hero 輪播 */
    let heroIndex = 0, heroTimer = null;
    const totalSlides = document.querySelectorAll('.hero-slide').length;
    function heroGo(n) {
      heroIndex = (n + totalSlides) % totalSlides;
      document.getElementById('heroSlides').style.transform = `translateX(-${heroIndex * 100}%)`;
      document.querySelectorAll('.hero-dot').forEach((d, i) => d.classList.toggle('active', i === heroIndex));
      clearInterval(heroTimer);
      heroTimer = setInterval(() => heroMove(1), 5000);
    }
    function heroMove(dir) { heroGo(heroIndex + dir); }
    heroGo(0);

    /* 加入購物車（示範） */
    let cartCount = 3;
    function addToCart(btn) {
      updateCartBadge(++cartCount);
      btn.classList.add('done');
      btn.innerHTML = '<i class="fa fa-check"></i> 已加入';
      setTimeout(() => { btn.classList.remove('done'); btn.innerHTML = '<i class="fa fa-cart-plus"></i> 加入購物車'; }, 1500);
    }
  </script>
</body>
</html>
<?php /**PATH D:\course_PHP\project1151013\resources\views/front/index.blade.php ENDPATH**/ ?>