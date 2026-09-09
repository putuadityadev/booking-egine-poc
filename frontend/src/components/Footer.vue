<template>
  <footer class="footer-wrapper">
    <div class="container footer-container">
      <div class="footer-columns-grid">
        <div class="footer-col">
          <h5 class="col-title">Support</h5>
          <ul class="col-links">
            <li><a href="#" @click.prevent>Help Center</a></li>
            <li><a href="#" @click.prevent>AirCover Protection</a></li>
            <li><a href="#" @click.prevent>Anti-discrimination</a></li>
            <li><a href="#" @click.prevent>Disability support</a></li>
            <li><a href="#" @click.prevent>Cancellation options</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h5 class="col-title">Loyalty & Membership</h5>
          <ul class="col-links">
            <li><a href="#" @click.prevent>OAuth 2.1 Central Identity</a></li>
            <li><a href="#" @click.prevent>Tier Benefits (Bronze to Diamond)</a></li>
            <li><a href="#" @click.prevent>Push Transaction V2 Gateway</a></li>
            <li><a href="#" @click.prevent>Jeevawasa Club Partners</a></li>
            <li><a href="#" @click.prevent>Redeem Reward Points</a></li>
          </ul>
        </div>

        <div class="footer-col">
          <h5 class="col-title">StayHub Portfolio</h5>
          <ul class="col-links">
            <li><a href="#" @click.prevent>Unagi Mas Villas Ubud</a></li>
            <li><a href="#" @click.prevent>Adiwana Alas Harum</a></li>
            <li><a href="#" @click.prevent>Adiwana Svarga Loka</a></li>
            <li><a href="#" @click.prevent>Grand Sahid City Hotel</a></li>
            <li><a href="#" @click.prevent>Curated Resort Experiences</a></li>
          </ul>
        </div>
      </div>

      <div class="footer-bottom-bar">
        <div class="legal-left">
          <span>© 2026 StayHub Multi-Property POC, Inc.</span>
          <span class="dot">•</span>
          <a href="#" @click.prevent>Privacy</a>
          <span class="dot">•</span>
          <a href="#" @click.prevent>Terms</a>
          <span class="dot">•</span>
          <a href="#" @click.prevent>Sitemap</a>
        </div>

        <div class="settings-right">
          <div class="setting-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="globe-icon">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="2" y1="12" x2="22" y2="12"></line>
              <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
            </svg>
            <span>English (US)</span>
          </div>
          <div class="setting-item">
            <span>IDR (Rp)</span>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<style scoped>
.footer-wrapper {
  background: var(--colors-canvas);
  border-top: 1px solid var(--colors-hairline-soft);
  padding: 48px 0 32px;
  margin-top: 64px;
}

.footer-columns-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 32px;
  padding-bottom: 40px;
  border-bottom: 1px solid var(--colors-hairline-soft);
}

.col-title {
  font-size: 14px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 16px;
}

.col-links {
  list-style: none;
  padding: 0;
  margin: 0;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.col-links a {
  font-size: 14px;
  color: var(--colors-body);
  transition: color 0.15s ease;
}

.col-links a:hover {
  text-decoration: underline;
  color: var(--colors-ink);
}

.footer-bottom-bar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 24px;
  font-size: 13px;
  color: var(--colors-body);
  flex-wrap: wrap;
  gap: 16px;
}

.legal-left {
  display: flex;
  align-items: center;
  gap: 8px;
  flex-wrap: wrap;
}

.legal-left a:hover {
  text-decoration: underline;
}

.dot {
  color: var(--colors-muted);
}

.settings-right {
  display: flex;
  align-items: center;
  gap: 20px;
  font-weight: 600;
  color: var(--colors-ink);
}

.setting-item {
  display: flex;
  align-items: center;
  gap: 6px;
  cursor: pointer;
}

.globe-icon {
  width: 16px;
  height: 16px;
}

@media (max-width: 744px) {
  .footer-columns-grid {
    grid-template-columns: 1fr;
  }
}
</style>
