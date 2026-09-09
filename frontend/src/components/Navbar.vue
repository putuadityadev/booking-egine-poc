<template>
  <header class="navbar-wrapper">
    <div class="container navbar-container">
      <!-- Brand Logo -->
      <div class="navbar-brand" @click="activeTab = 'stays'">
        <svg class="brand-icon" viewBox="0 0 32 32" fill="none">
          <path d="M16 1.5c-4.7 0-9.2 2.8-11.2 7.1-2 4.4-1.2 9.5 2 13.1l8.5 9.4c.4.4 1 .4 1.4 0l8.5-9.4c3.2-3.6 4-8.7 2-13.1C25.2 4.3 20.7 1.5 16 1.5zm0 15.5c-2.2 0-4-1.8-4-4s1.8-4 4-4 4 1.8 4 4-1.8 4-4 4z" fill="#FF385C" />
        </svg>
        <span class="brand-title">stayhub</span>
        <span class="brand-poc-tag">POC</span>
      </div>

      <!-- Center Product Tabs -->
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

        <!-- Property Switcher Dropdown -->
        <div class="property-dropdown-wrapper">
          <button class="nav-tab property-select-btn" @click="isPropertyMenuOpen = !isPropertyMenuOpen">
            <svg class="tab-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
              <line x1="9" y1="3" x2="9" y2="21"></line>
            </svg>
            <span class="property-active-name">{{ activeProperty?.name || 'Select Hotel' }}</span>
            <svg class="chevron-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
          </button>

          <div v-if="isPropertyMenuOpen" class="property-dropdown-menu">
            <div class="dropdown-header">Switch Hotel Property</div>
            <button
              v-for="prop in properties"
              :key="prop.id"
              class="property-menu-item"
              :class="{ selected: prop.id === activePropertyId }"
              @click="handlePropertySwitch(prop.id)"
            >
              <img :src="prop.image_url" :alt="prop.name" class="prop-thumb" />
              <div class="prop-info">
                <div class="prop-name">{{ prop.name }}</div>
                <div class="prop-city">{{ prop.city }} • ★ {{ prop.review_score }}</div>
              </div>
              <span v-if="prop.has_membership" class="club-chip">Club</span>
            </button>
            <div class="dropdown-divider"></div>
            <router-link to="/extranet" class="extranet-dropdown-btn" @click="isPropertyMenuOpen = false">
              <span>⚙ Extranet Channel Setup</span>
              <span class="arrow">→</span>
            </router-link>
          </div>
        </div>
      </nav>

      <!-- Right Account Utilities -->
      <div class="navbar-actions">
        <!-- Extranet Link Button -->
        <router-link to="/extranet" class="btn-extranet-nav" title="Manage PMS and OAuth Credentials">
          <span>Extranet</span>
        </router-link>

        <!-- Club Status Pill (Refined Luxury Sanctuary Chip) -->
        <div v-if="activeProperty?.has_membership" class="club-status-pill">
          <span class="club-diamond-icon">💎</span>
          <span>Jeevawasa Sanctuary</span>
        </div>

        <!-- Logged In Member State -->
        <div v-if="isLoggedIn" class="user-profile-wrapper">
          <button class="user-pill-btn logged-in" @click="isUserMenuOpen = !isUserMenuOpen">
            <div class="tier-indicator" :class="tierClass">{{ memberTier }}</div>
            <div class="points-pill">💎 {{ memberPoints }} Pts</div>
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
          <div v-if="isUserMenuOpen" class="user-dropdown-menu">
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
              <span class="meta-label">Club Tenant:</span>
              <span class="meta-value">{{ activeProperty?.membership?.tenant_domain || 'jeevawasa.localhost' }}</span>
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

        <!-- Guest State (Not Logged In) -->
        <div v-else class="guest-login-group">
          <button class="btn-guest-login" @click="openAuthModal('otp')">
            <svg class="user-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
              <circle cx="12" cy="7" r="4"></circle>
            </svg>
            <span>Member Sign In</span>
          </button>
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed } from 'vue'
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
  background-color: var(--colors-canvas);
  border-bottom: 1px solid var(--colors-hairline-soft);
  position: sticky;
  top: 0;
  z-index: 100;
}

.navbar-container {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

/* Brand */
.navbar-brand {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  user-select: none;
}

.brand-icon {
  width: 34px;
  height: 34px;
}

.brand-title {
  font-size: 22px;
  font-weight: 700;
  letter-spacing: -0.5px;
  color: var(--colors-primary);
}

.brand-poc-tag {
  background: var(--colors-surface-strong);
  color: var(--colors-muted);
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
  letter-spacing: 0.5px;
}

/* Tabs */
.navbar-tabs {
  display: flex;
  align-items: center;
  gap: 16px;
}

.nav-tab {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 16px;
  font-size: 15px;
  font-weight: 500;
  color: var(--colors-muted);
  position: relative;
  transition: color 0.2s ease;
  border-radius: var(--radius-full);
}

.nav-tab:hover {
  color: var(--colors-ink);
  background-color: var(--colors-surface-soft);
}

.nav-tab.active {
  color: var(--colors-ink);
  font-weight: 600;
}

.nav-tab.active::after {
  content: '';
  position: absolute;
  bottom: -16px;
  left: 16px;
  right: 16px;
  height: 2px;
  background-color: var(--colors-ink);
}

.tab-icon {
  width: 18px;
  height: 18px;
}

/* Property Dropdown */
.property-dropdown-wrapper {
  position: relative;
}

.property-select-btn {
  background-color: var(--colors-surface-soft);
  border: 1px solid var(--colors-hairline);
  color: var(--colors-ink);
}

.property-active-name {
  max-width: 180px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.chevron-icon {
  width: 14px;
  height: 14px;
  color: var(--colors-muted);
}

.property-dropdown-menu {
  position: absolute;
  top: 52px;
  left: 0;
  width: 320px;
  background: var(--colors-canvas);
  border-radius: var(--radius-md);
  border: 1px solid var(--colors-hairline);
  box-shadow: none;
  padding: 8px;
  z-index: 100;
}

.dropdown-header {
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--colors-muted);
  font-weight: 700;
  padding: 6px 10px;
}

.property-menu-item {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 8px 10px;
  border-radius: var(--radius-sm);
  text-align: left;
  transition: background-color 0.15s ease;
}

.property-menu-item:hover {
  background-color: var(--colors-surface-soft);
}

.property-menu-item.selected {
  background-color: #fff1f2;
}

.prop-thumb {
  width: 44px;
  height: 44px;
  border-radius: var(--radius-sm);
  object-fit: cover;
}

.prop-info {
  flex: 1;
  min-width: 0;
}

.prop-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--colors-ink);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.prop-city {
  font-size: 12px;
  color: var(--colors-muted);
}

.club-chip {
  background: #ede9fe;
  color: #6d28d9;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
}

/* Actions */
.navbar-actions {
  display: flex;
  align-items: center;
  gap: 16px;
}

.club-status-pill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: var(--colors-surface-soft);
  color: var(--colors-ink);
  border: 1px solid var(--colors-hairline-soft);
  padding: 6px 14px;
  border-radius: var(--radius-full);
  font-size: 12px;
  font-weight: 600;
}

.club-diamond-icon {
  font-size: 11px;
}

.pulse-dot {
  width: 8px;
  height: 8px;
  border-radius: 50%;
  background-color: #22c55e;
  box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
}

.btn-extranet-nav {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #f1f5f9;
  color: #334155;
  border: 1px solid #cbd5e1;
  padding: 6px 12px;
  border-radius: var(--radius-full);
  font-size: 12px;
  font-weight: 700;
  transition: all 0.15s ease;
}

.btn-extranet-nav:hover {
  background: #0f172a;
  color: #ffffff;
  border-color: #0f172a;
}

.extranet-dropdown-btn {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 8px 10px;
  font-size: 13px;
  font-weight: 600;
  color: #4338ca;
  background: #eef2ff;
  border-radius: var(--radius-sm);
  transition: background-color 0.15s ease;
  text-decoration: none;
}

.extranet-dropdown-btn:hover {
  background: #e0e7ff;
}

/* Guest Button */
.btn-guest-login {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  border-radius: var(--radius-full);
  border: 1px solid var(--colors-hairline);
  background: var(--colors-canvas);
  font-size: 14px;
  font-weight: 600;
  color: var(--colors-ink);
  box-shadow: none;
  transition: border-color 0.2s ease;
}

.btn-guest-login:hover {
  border-color: var(--colors-ink);
}

.user-icon {
  width: 18px;
  height: 18px;
  color: var(--colors-muted);
}

/* Member State */
.user-profile-wrapper {
  position: relative;
}

.user-pill-btn {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 6px 8px 6px 14px;
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline);
  border-radius: var(--radius-full);
  box-shadow: none;
  transition: border-color 0.2s ease;
}

.user-pill-btn:hover {
  border-color: var(--colors-ink);
}

.tier-indicator {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
}

.points-pill {
  font-size: 13px;
  font-weight: 700;
  color: var(--colors-ink);
  background: var(--colors-surface-soft);
  padding: 3px 8px;
  border-radius: var(--radius-full);
}

.user-avatar-circle {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: var(--colors-primary);
  color: white;
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
  top: 52px;
  right: 0;
  width: 280px;
  background: var(--colors-canvas);
  border-radius: var(--radius-md);
  border: 1px solid var(--colors-hairline);
  box-shadow: none;
  padding: 16px;
  z-index: 110;
}

.user-card-header {
  margin-bottom: 12px;
}

.user-name {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
}

.user-email {
  font-size: 13px;
  color: var(--colors-muted);
  margin-bottom: 8px;
}

.tier-badge-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 6px;
}

.points-count {
  font-size: 12px;
  font-weight: 600;
  color: var(--colors-primary);
}

.dropdown-divider {
  height: 1px;
  background: var(--colors-hairline-soft);
  margin: 10px 0;
}

.dropdown-meta-item {
  display: flex;
  justify-content: space-between;
  font-size: 12px;
  padding: 4px 0;
}

.meta-label {
  color: var(--colors-muted);
}

.meta-value {
  font-weight: 600;
  color: var(--colors-ink);
}

.dropdown-logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px;
  font-size: 14px;
  font-weight: 600;
  color: var(--colors-error);
  border-radius: var(--radius-sm);
  transition: background-color 0.15s ease;
}

.dropdown-logout-btn:hover {
  background: #fef2f2;
}

.logout-icon {
  width: 16px;
  height: 16px;
}
</style>
