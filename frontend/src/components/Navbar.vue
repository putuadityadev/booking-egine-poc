<template>
  <header class="navbar-wrapper">
    <div class="container navbar-container">
      <!-- Left: Dynamic Hotel Property Logo & Name with Mini Dropdown Switcher -->
      <div class="navbar-left">
        <div class="property-brand-zone">
          <!-- Property Logo & Name (Click to reset to stays tab) -->
          <div class="property-brand-main" @click="activeTab = 'stays'">
            <img
              :src="currentPropertyLogo"
              :alt="activeProperty?.name || 'Sanctuary Logo'"
              class="property-logo-img"
              @error="handleLogoError"
            />
            <div class="property-naming">
              <span class="property-name-title">{{ activeProperty?.name || 'Unagi Mas Villas Ubud' }}</span>
              <span class="property-location-sub">{{ activeProperty?.city || 'Ubud, Bali' }}</span>
            </div>
          </div>

          <!-- Mini Dropdown Trigger Button ("dropdown kecil") -->
          <div class="property-switcher-wrapper">
            <button
              type="button"
              class="btn-mini-property-toggle"
              :class="{ 'is-active': isPropertyMenuOpen }"
              @click.stop="isPropertyMenuOpen = !isPropertyMenuOpen"
              title="Switch Hotel Sanctuary"
              aria-label="Switch hotel sanctuary"
            >
              <svg
                class="chevron-mini-svg"
                :class="{ 'rotated': isPropertyMenuOpen }"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2.5"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <polyline points="6 9 12 15 18 9" />
              </svg>
            </button>

            <!-- Compact Property Switcher Dropdown Menu -->
            <div v-if="isPropertyMenuOpen" class="property-compact-menu" @click.stop>
              <div class="compact-menu-header">Select Hotel Sanctuary</div>
              <div class="compact-menu-list">
                <button
                  v-for="prop in properties"
                  :key="prop.id"
                  type="button"
                  class="compact-prop-item"
                  :class="{ selected: prop.id === activePropertyId }"
                  @click="handlePropertySwitch(prop.id)"
                >
                  <img :src="getPropertyLogo(prop)" :alt="prop.name" class="compact-prop-logo" />
                  <div class="compact-prop-text">
                    <div class="compact-prop-name">{{ prop.name }}</div>
                    <div class="compact-prop-location">
                      <span>{{ prop.city }}</span>
                      <template v-if="prop.review_score">
                        <span class="dot-sep">&bull;</span>
                        <span class="rating-val">&starf; {{ prop.review_score }}</span>
                      </template>
                    </div>
                  </div>
                  <div v-if="prop.id === activePropertyId" class="selected-check-indicator">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="check-mini-svg">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Center: Clean Product Navigation Tabs -->
      <nav class="navbar-tabs">
        <button
          class="nav-tab"
          :class="{ active: activeTab === 'stays' }"
          @click="activeTab = 'stays'"
        >
          <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
          </svg>
          <span>Stays</span>
        </button>

        <button
          class="nav-tab"
          :class="{ active: activeTab === 'experiences' }"
          @click="activeTab = 'experiences'"
        >
          <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
          </svg>
          <span>Experiences</span>
          <span class="badge-new">NEW</span>
        </button>
      </nav>

      <!-- Right: Prominent Member Sign In (Primary Button) / Logged-in Profile -->
      <div class="navbar-actions">
        <!-- Logged In Member State -->
        <div v-if="isLoggedIn" class="user-profile-wrapper">
          <button class="user-pill-btn logged-in" @click.stop="isUserMenuOpen = !isUserMenuOpen">
            <div class="tier-indicator" :class="tierClass">{{ memberTier }}</div>
            <div class="points-pill">
              <svg class="points-pill-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
              </svg>
              <span>{{ memberPoints }} Pts</span>
            </div>
            <div class="user-avatar-circle">
              <img
                v-if="memberProfile?.avatar_url"
                :src="memberProfile.avatar_url"
                :alt="memberName"
                class="avatar-img"
              />
              <span v-else class="avatar-initials">{{ memberInitials }}</span>
            </div>
          </button>

          <!-- User Dropdown Menu -->
          <div v-if="isUserMenuOpen" class="user-dropdown-menu" @click.stop>
            <div class="user-card-header">
              <div class="user-name">{{ memberName }}</div>
              <div class="user-email">{{ memberEmail }}</div>
              <div class="tier-badge-row">
                <span class="badge-member-tier" :class="tierClass">{{ memberTier }} Member</span>
                <span class="points-count">{{ memberPoints }} Reward Points</span>
              </div>
            </div>
            <div class="dropdown-divider"></div>
            <div class="dropdown-meta-item">
              <span class="meta-label">Member ID:</span>
              <span class="meta-value">{{ memberProfile?.member?.member_code || 'MBR-7JWRY7' }}</span>
            </div>
            <div class="dropdown-meta-item">
              <span class="meta-label">Loyalty Club:</span>
              <span class="meta-value">{{ activeProperty?.name || 'Jeevawasa Sanctuary' }}</span>
            </div>
            <div class="dropdown-divider"></div>
            <button class="dropdown-logout-btn" @click="handleLogout">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="logout-icon">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                <polyline points="16 17 21 12 16 7"></polyline>
                <line x1="21" y1="12" x2="9" y2="12"></line>
              </svg>
              <span>Sign Out</span>
            </button>
          </div>
        </div>

        <!-- Guest State: Prominent Primary Action Button -->
        <div v-else class="guest-login-group">
          <button class="btn-guest-login-primary" @click="openAuthModal('otp')">
            <svg class="signin-diamond-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
            <span>Member Sign In</span>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import { useBooking } from '../composables/useBooking'

const router = useRouter()

const {
  isLoggedIn,
  memberProfile,
  memberName,
  memberEmail,
  memberTier,
  memberPoints,
  openAuthModal,
  logout
} = useAuth()

const {
  properties,
  activePropertyId,
  activeProperty,
  activeTab,
  switchProperty
} = useBooking()

const isPropertyMenuOpen = ref(false)
const isUserMenuOpen = ref(false)

// Close dropdowns on outside click
const handleClickOutside = () => {
  isPropertyMenuOpen.value = false
  isUserMenuOpen.value = false
}

onMounted(() => {
  window.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside)
})

// Property logo map linking property id or code to generated transparent png logo
const propertyLogos = {
  5: '/logos/unagi-mas.png',
  6: '/logos/adiwana-alas-harum.png',
  7: '/logos/adiwana-svarga-loka.png',
  8: '/logos/grand-sahid.png',
  'UMV-UBUD': '/logos/unagi-mas.png',
  'AAH-UBUD': '/logos/adiwana-alas-harum.png',
  'ASL-UBUD': '/logos/adiwana-svarga-loka.png',
  'GSH-JKT': '/logos/grand-sahid.png',
}

const getPropertyLogo = (prop) => {
  if (!prop) return '/logos/unagi-mas.png'
  return propertyLogos[prop.id] || propertyLogos[prop.code] || '/hotel-brand-logo.svg'
}

const currentPropertyLogo = computed(() => {
  return getPropertyLogo(activeProperty.value)
})

const handleLogoError = (e) => {
  e.target.src = '/hotel-brand-logo.svg'
}

const tierClass = computed(() => {
  const tier = (memberTier.value || '').toLowerCase()
  if (tier.includes('diamond')) return 'badge-diamond'
  if (tier.includes('gold')) return 'badge-gold'
  if (tier.includes('silver')) return 'badge-silver'
  return 'badge-bronze'
})

const memberInitials = computed(() => {
  if (!memberName.value) return 'M'
  const parts = memberName.value.trim().split(' ')
  if (parts.length >= 2) return `${parts[0][0]}${parts[1][0]}`.toUpperCase()
  return parts[0][0].toUpperCase()
})

const handlePropertySwitch = async (id) => {
  isPropertyMenuOpen.value = false
  await switchProperty(id)
  router.push(`/book/${id}`)
}

const handleLogout = async () => {
  isUserMenuOpen.value = false
  await logout(activePropertyId.value)
}
</script>

<style scoped>
.navbar-wrapper {
  height: 80px;
  background-color: var(--colors-canvas, #ffffff);
  border-bottom: 1px solid var(--colors-hairline-soft, #ebebeb);
  position: sticky;
  top: 0;
  z-index: 100;
}

.navbar-container {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
}

/* Left: Property Branding & Mini Dropdown */
.navbar-left {
  display: flex;
  align-items: center;
}

.property-brand-zone {
  display: flex;
  align-items: center;
  gap: 8px;
  position: relative;
}

.property-brand-main {
  display: flex;
  align-items: center;
  gap: 12px;
  cursor: pointer;
  user-select: none;
}

.property-logo-img {
  width: 44px;
  height: 44px;
  object-fit: contain;
  border-radius: 8px;
  flex-shrink: 0;
}

.property-naming {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.property-name-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--colors-ink, #1a1a1a);
  letter-spacing: -0.2px;
  line-height: 1.2;
  white-space: nowrap;
}

.property-location-sub {
  font-size: 11px;
  color: var(--colors-muted, #717171);
  font-weight: 500;
  line-height: 1;
}

/* Mini Switcher Toggle Button ("dropdown kecil") */
.property-switcher-wrapper {
  position: relative;
}

.btn-mini-property-toggle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: var(--colors-canvas, #ffffff);
  border: 1px solid var(--colors-hairline, #e0e0e0);
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  padding: 0;
  color: #475569;
  transition: all 0.2s ease;
}

.btn-mini-property-toggle:hover {
  background: #f1f5f9;
  border-color: var(--colors-ink, #1a1a1a);
  color: var(--colors-ink, #1a1a1a);
}

.chevron-mini-svg {
  width: 12px;
  height: 12px;
  transition: transform 0.2s ease;
}

.chevron-mini-svg.rotated {
  transform: rotate(180deg);
}

/* Compact Dropdown Menu */
.property-compact-menu {
  position: absolute;
  top: 36px;
  left: 0;
  width: 320px;
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid var(--colors-hairline, #e0e0e0);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  padding: 8px;
  z-index: 200;
}

.compact-menu-header {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.6px;
  color: #64748b;
  font-weight: 700;
  padding: 8px 12px 6px;
  border-bottom: 1px solid #f1f5f9;
  margin-bottom: 4px;
}

.compact-menu-list {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.compact-prop-item {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 10px;
  border-radius: 8px;
  background: transparent;
  border: 1px solid transparent;
  width: 100%;
  text-align: left;
  cursor: pointer;
  transition: all 0.15s ease;
}

.compact-prop-item:hover {
  background-color: #f8fafc;
}

.compact-prop-item.selected {
  background-color: #f0fdf4;
  border-color: #bbf7d0;
}

.compact-prop-logo {
  width: 36px;
  height: 36px;
  object-fit: contain;
  border-radius: 6px;
  flex-shrink: 0;
}

.compact-prop-text {
  flex: 1;
  min-width: 0;
}

.compact-prop-name {
  font-size: 13px;
  font-weight: 600;
  color: var(--colors-ink, #1a1a1a);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.compact-prop-location {
  font-size: 11px;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 4px;
  margin-top: 2px;
}

.dot-sep {
  color: #94a3b8;
}

.rating-val {
  color: #b45309;
  font-weight: 600;
}

.selected-check-indicator {
  width: 18px;
  height: 18px;
  border-radius: 50%;
  background: #16a34a;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.check-mini-svg {
  width: 12px;
  height: 12px;
}

/* Center Tabs */
.navbar-tabs {
  display: flex;
  align-items: center;
  gap: 12px;
}

.nav-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  font-size: 14px;
  font-weight: 500;
  color: var(--colors-muted, #717171);
  position: relative;
  transition: all 0.2s ease;
  border-radius: var(--radius-full, 9999px);
  background: transparent;
  border: none;
  cursor: pointer;
}

.nav-tab:hover {
  color: var(--colors-ink, #1a1a1a);
  background-color: #f8fafc;
}

.nav-tab.active {
  color: var(--colors-ink, #1a1a1a);
  font-weight: 700;
}

.nav-tab.active::after {
  content: '';
  position: absolute;
  bottom: -16px;
  left: 18px;
  right: 18px;
  height: 2px;
  background-color: var(--colors-ink, #1a1a1a);
}

.tab-icon {
  width: 16px;
  height: 16px;
}

.badge-new {
  font-size: 9px;
  font-weight: 800;
  color: #2563eb;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  padding: 1px 5px;
  border-radius: 4px;
  letter-spacing: 0.5px;
}

/* Right Actions */
.navbar-actions {
  display: flex;
  align-items: center;
}

/* Prominent Primary Member Sign In Button */
.btn-guest-login-primary {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 22px;
  border-radius: var(--radius-full, 9999px);
  background: var(--colors-ink, #1a1a1a);
  color: #ffffff;
  font-size: 13px;
  font-weight: 700;
  border: none;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: none;
}

.btn-guest-login-primary:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

.signin-diamond-svg {
  width: 15px;
  height: 15px;
  color: #f59e0b;
}

/* Logged-In User Profile Pill */
.user-profile-wrapper {
  position: relative;
}

.user-pill-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 8px 6px 14px;
  background: var(--colors-canvas, #ffffff);
  border: 1px solid var(--colors-hairline, #e0e0e0);
  border-radius: var(--radius-full, 9999px);
  box-shadow: none;
  cursor: pointer;
  transition: border-color 0.2s ease;
}

.user-pill-btn:hover {
  border-color: var(--colors-ink, #1a1a1a);
}

.tier-indicator {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.points-pill {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  font-weight: 700;
  color: var(--colors-ink, #1a1a1a);
  background: #f1f5f9;
  padding: 3px 8px;
  border-radius: var(--radius-full, 9999px);
}

.points-pill-svg {
  width: 12px;
  height: 12px;
  color: #b45309;
}

.user-avatar-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #0f172a;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
  font-size: 12px;
  overflow: hidden;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

/* User Dropdown */
.user-dropdown-menu {
  position: absolute;
  top: 48px;
  right: 0;
  width: 280px;
  background: #ffffff;
  border-radius: 12px;
  border: 1px solid var(--colors-hairline, #e0e0e0);
  box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
  padding: 16px;
  z-index: 110;
}

.user-card-header {
  margin-bottom: 12px;
}

.user-name {
  font-size: 15px;
  font-weight: 700;
  color: var(--colors-ink, #1a1a1a);
}

.user-email {
  font-size: 12px;
  color: var(--colors-muted, #717171);
  margin-bottom: 8px;
}

.tier-badge-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 6px;
}

.badge-member-tier {
  font-size: 10px;
  font-weight: 800;
  padding: 2px 8px;
  border-radius: 12px;
  text-transform: uppercase;
}

.badge-diamond {
  background: #fdf4ff;
  color: #86198f;
  border: 1px solid #f0abfc;
}

.badge-gold {
  background: #fefce8;
  color: #854d0e;
  border: 1px solid #fde047;
}

.badge-silver {
  background: #f1f5f9;
  color: #475569;
  border: 1px solid #cbd5e1;
}

.badge-bronze {
  background: #fff7ed;
  color: #9a3412;
  border: 1px solid #fed7aa;
}

.points-count {
  font-size: 12px;
  font-weight: 700;
  color: #701a75;
}

.dropdown-divider {
  height: 1px;
  background: #f1f5f9;
  margin: 10px 0;
}

.dropdown-meta-item {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  padding: 4px 0;
}

.meta-label {
  color: var(--colors-muted, #717171);
}

.meta-value {
  font-weight: 600;
  color: var(--colors-ink, #1a1a1a);
}

.dropdown-logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 10px;
  font-size: 13px;
  font-weight: 600;
  color: #dc2626;
  border-radius: 6px;
  background: transparent;
  border: none;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.dropdown-logout-btn:hover {
  background: #fef2f2;
}

.logout-icon {
  width: 16px;
  height: 16px;
}

@media (max-width: 900px) {
  .property-name-title {
    max-width: 160px;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  .navbar-tabs {
    display: none;
  }
}
</style>
