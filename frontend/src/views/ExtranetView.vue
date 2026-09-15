<template>
  <div class="extranet-root">
    <!-- Top Extranet Header Bar -->
    <header class="extranet-header">
      <div class="container header-content">
        <div class="brand-group">
          <div class="hub-logo-wrap">
            <svg class="hub-icon-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
            </svg>
            <span class="hub-logo">stayhub</span>
          </div>
          <span class="extranet-badge">EXTRANET</span>
          <span class="sub-caption">Channel &amp; Property Management</span>
        </div>

        <div class="header-nav-actions">
          <router-link :to="`/book/${selectedPropertyId || 1}`" class="btn-public-engine">
            <span>View Booking Engine</span>
            <svg class="arrow-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M7 17 17 7" />
              <path d="M7 7h10v10" />
            </svg>
          </router-link>
        </div>
      </div>
    </header>

    <!-- Main Extranet Body -->
    <main class="container extranet-main">
      <div class="extranet-layout-grid">
        <!-- Left Sidebar: Property Selector List -->
        <aside class="property-sidebar">
          <div class="sidebar-header">
            <h3 class="sidebar-title">Properties Portfolio</h3>
            <span class="prop-count-tag">{{ properties.length }} Hotels</span>
          </div>

          <div class="property-list">
            <div
              v-for="prop in properties"
              :key="prop.id"
              class="prop-list-card"
              :class="{ active: prop.id === selectedPropertyId }"
              @click="selectProperty(prop)"
            >
              <img :src="prop.image_url" :alt="prop.name" class="prop-thumb-img" />
              <div class="prop-card-body">
                <div class="prop-card-name">{{ prop.name }}</div>
                <div class="prop-card-city">{{ prop.city }}</div>
                <div class="prop-status-row">
                  <span
                    class="status-indicator-pill"
                    :class="prop.has_membership ? 'status-connected' : 'status-disconnected'"
                  >
                    <span class="dot"></span>
                    <span>{{ prop.has_membership ? 'OAuth Connected' : 'Standalone' }}</span>
                  </span>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <!-- Right Content: Property & Dynamic Rate Plan Management -->
        <section v-if="selectedProperty" class="editor-section">
          <!-- Property Header Banner -->
          <div class="editor-header-banner">
            <div>
              <div class="editor-kicker">PROPERTY MANAGEMENT</div>
              <h2 class="editor-title">{{ selectedProperty.name }}</h2>
              <p class="editor-subtitle">{{ selectedProperty.tagline }} • {{ selectedProperty.city }}</p>
            </div>

            <router-link :to="`/book/${selectedProperty.id}`" class="btn-preview-property" target="_blank">
              <span>Open in Booking Engine</span>
              <svg class="arrow-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M7 17 17 7" />
                <path d="M7 7h10v10" />
              </svg>
            </router-link>
          </div>

          <!-- Alert Notifications -->
          <div v-if="successMessage" class="extranet-alert alert-success">
            <svg class="alert-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <polyline points="20 6 9 17 4 12" />
            </svg>
            <span>{{ successMessage }}</span>
          </div>

          <div v-if="errorMessage" class="extranet-alert alert-error">
            <svg class="alert-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <circle cx="12" cy="12" r="10" />
              <line x1="12" y1="8" x2="12" y2="12" />
              <line x1="12" y1="16" x2="12.01" y2="16" />
            </svg>
            <span>{{ errorMessage }}</span>
          </div>

          <!-- Clean Tab Navigation -->
          <div class="editor-tabs-bar">
            <button
              type="button"
              class="editor-tab-btn"
              :class="{ active: activeSection === 'connection' }"
              @click="activeSection = 'connection'"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
              </svg>
              <span>Membership Connection</span>
            </button>

            <button
              type="button"
              class="editor-tab-btn"
              :class="{ active: activeSection === 'rate-plans' }"
              @click="activeSection = 'rate-plans'"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
              </svg>
              <span>Room Rate Plans (Dynamic Tiers)</span>
            </button>

            <button
              type="button"
              class="editor-tab-btn"
              :class="{ active: activeSection === 'details' }"
              @click="activeSection = 'details'"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18" />
                <path d="M5 21V7l8-4v18" />
                <path d="M19 21V11l-6-4" />
              </svg>
              <span>Hotel Details</span>
            </button>

            <button
              type="button"
              class="editor-tab-btn"
              :class="{ active: activeSection === 'bookings' }"
              @click="activeSection = 'bookings'; fetchBookings()"
            >
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
                <line x1="16" y1="13" x2="8" y2="13" />
                <line x1="16" y1="17" x2="8" y2="17" />
                <polyline points="10 9 9 9 8 9" />
              </svg>
              <span>Reservations &amp; Points Control</span>
            </button>
          </div>

          <!-- TAB 1: MEMBERSHIP INTEGRATION (NO MANUAL MERCHANT UUID) -->
          <div v-if="activeSection === 'connection'" class="config-card">
            <div class="card-title-row">
              <div class="card-icon-tag">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71" />
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71" />
                </svg>
              </div>
              <div>
                <h4 class="card-title">Membership Platform Connection</h4>
                <p class="card-desc">
                  Connect via OAuth 2.1 App Connection. Merchant and Corporate IDs are automatically discovered from the property context.
                </p>
              </div>
            </div>

            <div class="auto-resolution-info-box">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="16" x2="12" y2="12" />
                <line x1="12" y1="8" x2="12.01" y2="8" />
              </svg>
              <span>
                <strong>Zero Manual Merchant Mapping:</strong> You only need to provide the Tenant Domain, Client ID, and Client Secret. The system automatically queries the property context to resolve the exact Merchant and Branch UUID.
              </span>
            </div>

            <div class="form-grid">
              <div class="field-item">
                <label class="form-label">Tenant Domain</label>
                <input
                  v-model="form.membership.x_tenant_domain"
                  type="text"
                  placeholder="jeevawasa.localhost"
                  class="extranet-input"
                />
                <span class="field-hint">Tenant domain routing for multi-tenant database switch</span>
              </div>

              <div class="field-item">
                <label class="form-label">Client ID (AppConnection UUID)</label>
                <input
                  v-model="form.membership.client_id"
                  type="text"
                  placeholder="a0f17583-5f52-4a2c-bd5b-782e81f75052"
                  class="extranet-input monospace"
                />
                <span class="field-hint">Issued from central Membership Connections App</span>
              </div>

              <div class="field-item full-width">
                <label class="form-label">Client Secret</label>
                <input
                  v-model="form.membership.client_secret"
                  type="password"
                  placeholder="••••••••••••••••••••••••"
                  class="extranet-input monospace"
                />
                <span class="field-hint">Kept secure on backend BFF. Never exposed to browser clients.</span>
              </div>
            </div>

            <!-- Live Resolved Context Details -->
            <div v-if="testResult" class="test-result-box">
              <div class="test-result-header">
                <span class="badge-success">Live Connection Healthy</span>
                <span class="test-time">Verified Just Now</span>
              </div>

              <div class="resolved-context-grid">
                <img
                  v-if="testResult.context?.branding?.logo_url && !testLogoFailed"
                  :src="testResult.context.branding.logo_url"
                  alt="Brand Logo"
                  class="resolved-logo"
                  @error="testLogoFailed = true"
                />
                <div v-else class="resolved-logo-seal">
                  {{ (testResult.context?.corporate?.name || 'JW').slice(0, 2).toUpperCase() }}
                </div>
                <div class="resolved-meta">
                  <div class="res-line">
                    <strong>Corporate:</strong> {{ testResult.context?.corporate?.name || 'JEEVAWASA' }}
                  </div>
                  <div class="res-line">
                    <strong>Branch:</strong> {{ testResult.context?.branch?.name || selectedProperty.name }}
                  </div>
                  <div class="res-line">
                    <strong>Auto-Resolved Merchant ID:</strong>
                    <span class="res-id-chip">{{ testResult.merchant_id }}</span>
                  </div>
                  <div class="res-line">
                    <strong>Loyalty Tiers Available:</strong>
                    {{ (testResult.context?.tiers || []).map(t => t.name).join(', ') || 'Bronze, Silver, Gold, Diamond' }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Point Release Policy Selection -->
            <div class="point-release-section">
              <div class="section-divider-title">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <polyline points="12 6 12 12 16 14" />
                </svg>
                <span>Point Release Mechanism</span>
              </div>
              <p class="section-subtitle">
                Select when loyalty points are credited to member accounts.
              </p>

              <div class="point-release-cards-grid">
                <!-- Option 1: Automated / Instant -->
                <div
                  class="release-mode-card"
                  :class="{ active: form.point_release_mode === 'automated' }"
                  @click="form.point_release_mode = 'automated'"
                >
                  <div class="mode-card-top">
                    <div class="mode-radio-row">
                      <input
                        type="radio"
                        id="mode-automated"
                        value="automated"
                        v-model="form.point_release_mode"
                      />
                      <label for="mode-automated" class="mode-title">Automated</label>
                    </div>
                    <span class="mode-badge badge-instant">⚡ Real-Time</span>
                  </div>
                  <p class="mode-desc">
                    Points credited immediately upon booking.
                  </p>
                </div>

                <!-- Option 2: Check-In Release -->
                <div
                  class="release-mode-card"
                  :class="{ active: form.point_release_mode === 'checkin' }"
                  @click="form.point_release_mode = 'checkin'"
                >
                  <div class="mode-card-top">
                    <div class="mode-radio-row">
                      <input
                        type="radio"
                        id="mode-checkin"
                        value="checkin"
                        v-model="form.point_release_mode"
                      />
                      <label for="mode-checkin" class="mode-title">On Check-In</label>
                    </div>
                    <span class="mode-badge badge-scheduled">🏨 At Arrival</span>
                  </div>
                  <p class="mode-desc">
                    Points released when guest checks in.
                  </p>
                </div>

                <!-- Option 3: Check-Out Release -->
                <div
                  class="release-mode-card"
                  :class="{ active: form.point_release_mode === 'checkout' }"
                  @click="form.point_release_mode = 'checkout'"
                >
                  <div class="mode-card-top">
                    <div class="mode-radio-row">
                      <input
                        type="radio"
                        id="mode-checkout"
                        value="checkout"
                        v-model="form.point_release_mode"
                      />
                      <label for="mode-checkout" class="mode-title">On Check-Out</label>
                    </div>
                    <span class="mode-badge badge-completion">🛫 At Departure</span>
                  </div>
                  <p class="mode-desc">
                    Points released after stay is completed.
                  </p>
                </div>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="connection-actions-row">
              <button
                type="button"
                class="btn-secondary btn-test-conn"
                :disabled="isTestingConnection || !form.membership.client_id"
                @click="handleTestConnection"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="btn-svg">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                </svg>
                <span v-if="isTestingConnection">Testing Connection...</span>
                <span v-else>Test Live Connection</span>
              </button>

              <button
                type="button"
                class="btn-primary btn-save-conn"
                :disabled="isSaving"
                @click="handleSaveProperty"
              >
                <span v-if="isSaving">Saving Settings...</span>
                <span v-else>Save Connection Configuration</span>
              </button>
            </div>
          </div>

          <!-- TAB 2: DYNAMIC ROOM MEMBER RATE PLANS -->
          <div v-if="activeSection === 'rate-plans'" class="config-card">
            <div class="card-title-row with-action">
              <div class="card-title-left">
                <div class="card-icon-tag">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                </div>
                <div>
                  <h4 class="card-title">Dynamic Member Rate Plans per Room</h4>
                  <p class="card-desc">
                    Synchronize real loyalty tiers from Membership Platform and set granular percentage discounts per room.
                  </p>
                </div>
              </div>

              <button
                type="button"
                class="btn-sync-tiers"
                :disabled="isFetchingTiers"
                @click="syncTiers"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sync-svg" :class="{ 'spin-anim': isFetchingTiers }">
                  <path d="M21.5 2v6h-6M2.5 22v-6h6M2 11.5a10 10 0 0 1 18.8-4.3M22 12.5a10 10 0 0 1-18.8 4.2" />
                </svg>
                <span>{{ isFetchingTiers ? 'Syncing Tiers...' : 'Sync Tiers from Membership' }}</span>
              </button>
            </div>

            <!-- Synced Tiers Badges Strip -->
            <div class="tiers-strip-box">
              <span class="tiers-strip-label">Active Platform Tiers:</span>
              <div class="tiers-pills">
                <div v-for="tier in activeTiers" :key="tier.id" class="tier-chip">
                  <span class="tier-chip-name">{{ tier.name }}</span>
                  <span class="tier-chip-min">Min {{ formatShortCurrency(tier.transaction_value_min) }}</span>
                </div>
              </div>
            </div>

            <!-- Rooms List with Dynamic Rate Matrices -->
            <div class="room-plans-list">
              <div
                v-for="room in editableRooms"
                :key="room.id"
                class="room-plan-card"
              >
                <div class="room-plan-header">
                  <img :src="room.image_url" :alt="room.name" class="room-plan-thumb" />
                  <div class="room-plan-meta">
                    <h5 class="room-plan-name">{{ room.name }}</h5>
                    <div class="room-plan-sub">
                      <span>Base Rate: <strong>{{ formatCurrency(room.base_price) }}</strong> / night</span>
                      <span class="dot-sep">•</span>
                      <span>{{ room.capacity }} Guests</span>
                    </div>
                  </div>

                  <!-- Member Rate Enable Toggle -->
                  <div class="member-rate-toggle-wrap">
                    <label class="toggle-switch">
                      <input
                        type="checkbox"
                        v-model="room.is_member_rate_applicable"
                      />
                      <span class="slider"></span>
                    </label>
                    <span class="toggle-label">
                      {{ room.is_member_rate_applicable ? 'Member Rate Enabled' : 'Standard Rate Only' }}
                    </span>
                  </div>
                </div>

                <!-- Dynamic Tier Matrix Table (Shown if member rate is enabled) -->
                <div v-if="room.is_member_rate_applicable" class="tier-matrix-table-wrap">
                  <table class="tier-matrix-table">
                    <thead>
                      <tr>
                        <th>Tier Name</th>
                        <th>Applicable</th>
                        <th>Discount %</th>
                        <th>Final Nightly Rate</th>
                        <th>Guest Savings</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr v-for="tier in activeTiers" :key="tier.name">
                        <td class="tier-name-cell">
                          <span class="tier-dot"></span>
                          <span class="tier-bold">{{ tier.name }}</span>
                        </td>
                        <td>
                          <input
                            type="checkbox"
                            :checked="isTierActive(room, tier.name)"
                            @change="toggleTierActive(room, tier.name, $event.target.checked)"
                            class="tier-checkbox"
                          />
                        </td>
                        <td>
                          <div class="percent-input-wrapper">
                            <input
                              type="number"
                              min="0"
                              max="90"
                              :value="getTierRate(room, tier.name)"
                              :disabled="!isTierActive(room, tier.name)"
                              @input="updateTierRate(room, tier.name, $event.target.value)"
                              class="percent-input"
                            />
                            <span class="percent-sign">%</span>
                          </div>
                        </td>
                        <td class="rate-preview-cell">
                          {{ formatCurrency(calculateTierPrice(room, tier.name)) }}
                        </td>
                        <td class="savings-preview-cell">
                          <span v-if="isTierActive(room, tier.name) && getTierRate(room, tier.name) > 0" class="savings-tag">
                            Save {{ formatCurrency(calculateTierSavings(room, tier.name)) }}
                          </span>
                          <span v-else class="text-muted">-</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <!-- Footer Save Room Rate Plan -->
                <div class="room-plan-footer">
                  <span v-if="roomPlanSuccess[room.id]" class="room-saved-msg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="check-svg">
                      <polyline points="20 6 9 17 4 12" />
                    </svg>
                    Rate plan saved successfully!
                  </span>
                  <div class="spacer"></div>
                  <button
                    type="button"
                    class="btn-save-room-plan"
                    :disabled="isSavingRoomPlan[room.id]"
                    @click="handleSaveRoomRatePlan(room)"
                  >
                    <span v-if="isSavingRoomPlan[room.id]">Saving...</span>
                    <span v-else>Save Room Rate Plan</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- TAB 3: HOTEL METADATA DETAILS -->
          <div v-if="activeSection === 'details'" class="config-card">
            <div class="card-title-row">
              <div class="card-icon-tag">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M3 21h18" />
                  <path d="M5 21V7l8-4v18" />
                  <path d="M19 21V11l-6-4" />
                </svg>
              </div>
              <div>
                <h4 class="card-title">Property Details &amp; Metadata</h4>
                <p class="card-desc">Public hotel name, location, and hero cover photo displayed on the booking catalog.</p>
              </div>
            </div>

            <div class="form-grid">
              <div class="field-item">
                <label class="form-label">Hotel Name</label>
                <input v-model="form.name" type="text" class="extranet-input" />
              </div>

              <div class="field-item">
                <label class="form-label">Tagline</label>
                <input v-model="form.tagline" type="text" class="extranet-input" />
              </div>

              <div class="field-item">
                <label class="form-label">City, State</label>
                <input v-model="form.city" type="text" class="extranet-input" />
              </div>

              <div class="field-item">
                <label class="form-label">Country</label>
                <input v-model="form.country" type="text" class="extranet-input" />
              </div>

              <div class="field-item full-width">
                <label class="form-label">Hero Cover Image URL</label>
                <input v-model="form.image_url" type="url" class="extranet-input" />
              </div>

              <div class="field-item full-width">
                <label class="form-label">Full Description</label>
                <textarea v-model="form.description" rows="3" class="extranet-textarea"></textarea>
              </div>
            </div>

            <div class="connection-actions-row">
              <button
                type="button"
                class="btn-primary btn-save-conn"
                :disabled="isSaving"
                @click="handleSaveProperty"
              >
                <span v-if="isSaving">Saving Details...</span>
                <span v-else>Save Property Details</span>
              </button>
            </div>
          </div>

          <!-- TAB 4: RESERVATIONS & POINT STATUS MANAGEMENT -->
          <div v-if="activeSection === 'bookings'" class="config-card">
            <div class="card-title-row with-action">
              <div class="card-title-left">
                <div class="card-icon-tag">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                    <polyline points="14 2 14 8 20 8" />
                    <line x1="16" y1="13" x2="8" y2="13" />
                    <line x1="16" y1="17" x2="8" y2="17" />
                    <polyline points="10 9 9 9 8 9" />
                  </svg>
                </div>
                <div>
                  <h4 class="card-title">Reservations &amp; Points Control</h4>
                  <p class="card-desc">
                    Manage guest stays and loyalty point releases.
                  </p>
                </div>
              </div>

              <button
                type="button"
                class="btn-sync-tiers"
                :disabled="isLoadingBookings"
                @click="fetchBookings"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="btn-svg" :class="{ 'spin-anim': isLoadingBookings }">
                  <polyline points="23 4 23 10 17 10" />
                  <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10" />
                </svg>
                <span>{{ isLoadingBookings ? 'Refreshing...' : 'Refresh' }}</span>
              </button>
            </div>

            <!-- Point Release Policy Summary Banner -->
            <div class="policy-summary-banner">
              <div class="policy-banner-item">
                <span class="banner-label">Active Policy:</span>
                <span class="banner-value">
                  <span v-if="form.point_release_mode === 'automated'" class="mode-chip chip-automated">⚡ Automated</span>
                  <span v-else-if="form.point_release_mode === 'checkin'" class="mode-chip chip-checkin">🏨 On Check-In</span>
                  <span v-else class="mode-chip chip-checkout">🛫 On Check-Out</span>
                </span>
              </div>
              <div class="policy-banner-item">
                <span class="banner-label">Total Bookings:</span>
                <span class="banner-number">{{ bookings.length }}</span>
              </div>
            </div>

            <!-- Bookings Table -->
            <div v-if="isLoadingBookings" class="bookings-loading-state">
              <span class="spinner-dot"></span>
              <span>Loading reservations...</span>
            </div>

            <div v-else-if="bookings.length === 0" class="bookings-empty-state">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="empty-svg">
                <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                <line x1="8" y1="21" x2="16" y2="21" />
                <line x1="12" y1="17" x2="12" y2="21" />
              </svg>
              <div class="empty-title">No Reservations Recorded</div>
              <p class="empty-desc">Reservations created on the public engine will appear here.</p>
            </div>

            <div v-else class="bookings-table-wrapper">
              <table class="bookings-table">
                <thead>
                  <tr>
                    <th class="col-code">Booking Code</th>
                    <th class="col-guest">Guest</th>
                    <th class="col-room">Room &amp; Dates</th>
                    <th class="col-mode">Release Mode</th>
                    <th class="col-points">Points</th>
                    <th class="col-status">Stay Status</th>
                    <th class="col-actions text-right">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="b in bookings" :key="b.id">
                    <td class="col-code">
                      <div class="code-cell">
                        <span class="booking-ref-code">{{ b.reservation_code }}</span>
                        <span class="booking-created-at">{{ formatDateShort(b.created_at) }}</span>
                      </div>
                    </td>
                    <td class="col-guest">
                      <div class="guest-cell">
                        <div class="guest-name-row">
                          <span class="guest-name">{{ b.guest_name }}</span>
                          <span v-if="b.is_member" class="member-tag">Member</span>
                          <span v-else class="guest-tag">Guest</span>
                        </div>
                        <span class="guest-email" :title="b.guest_email">{{ b.guest_email }}</span>
                      </div>
                    </td>
                    <td class="col-room">
                      <div class="room-date-cell">
                        <span class="room-title" :title="b.room?.name || 'Standard Suite'">{{ b.room?.name || 'Standard Suite' }}</span>
                        <span class="dates-range">{{ formatDateOnly(b.check_in) }} – {{ formatDateOnly(b.check_out) }} • {{ b.nights }}N</span>
                      </div>
                    </td>
                    <td class="col-mode">
                      <span v-if="b.point_release_mode === 'automated'" class="mode-pill pill-auto">⚡ Instant</span>
                      <span v-else-if="b.point_release_mode === 'checkin'" class="mode-pill pill-checkin">🏨 Check-in</span>
                      <span v-else class="mode-pill pill-checkout">🛫 Check-out</span>
                    </td>
                    <td class="col-points">
                      <div class="points-cell">
                        <div class="points-row">
                          <span class="points-val">{{ b.points_earned ? `+${b.points_earned} Pts` : '0 Pts' }}</span>
                          <span
                            v-if="b.is_points_materialized"
                            class="materialize-badge badge-done"
                          >
                            ✓ Released
                          </span>
                          <span
                            v-else-if="b.is_member"
                            class="materialize-badge badge-pending"
                          >
                            ⏳ Pending
                          </span>
                          <span v-else class="materialize-badge badge-none">—</span>
                        </div>
                      </div>
                    </td>
                    <td class="col-status">
                      <span class="status-chip" :class="`status-${(b.status || 'CONFIRMED').toLowerCase()}`">
                        {{ b.status || 'CONFIRMED' }}
                      </span>
                    </td>
                    <td class="col-actions text-right">
                      <div class="actions-cell">
                        <!-- Check-in button -->
                        <button
                          v-if="b.status === 'CONFIRMED'"
                          type="button"
                          class="btn-action-sm btn-checkin"
                          :disabled="isActionLoading[b.id]"
                          @click="handleUpdateStatus(b.id, 'CHECKED_IN')"
                        >
                          Check In
                        </button>

                        <!-- Check-out button -->
                        <button
                          v-if="b.status === 'CHECKED_IN'"
                          type="button"
                          class="btn-action-sm btn-checkout"
                          :disabled="isActionLoading[b.id]"
                          @click="handleUpdateStatus(b.id, 'CHECKED_OUT')"
                        >
                          Check Out
                        </button>

                        <!-- Manual Release Points button -->
                        <button
                          v-if="b.is_member && !b.is_points_materialized"
                          type="button"
                          class="btn-action-sm btn-release-point"
                          :disabled="isActionLoading[b.id]"
                          @click="handleMaterializePoints(b.id)"
                        >
                          <span v-if="isActionLoading[b.id]">Releasing...</span>
                          <span v-else>Release Pts</span>
                        </button>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'

const bffUrl = (import.meta.env.VITE_BFF_API_URL !== undefined && import.meta.env.VITE_BFF_API_URL !== '')
  ? import.meta.env.VITE_BFF_API_URL
  : (import.meta.env.DEV ? 'http://localhost:8002' : '')

const properties = ref([])
const selectedPropertyId = ref(1)
const selectedProperty = ref(null)

const activeSection = ref('connection') // 'connection' | 'rate-plans' | 'details'

const isTestingConnection = ref(false)
const isSaving = ref(false)
const testResult = ref(null)
const testLogoFailed = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

// Tiers state
const activeTiers = ref([
  { id: 'bronze', name: 'Bronze', transaction_value_min: 0 },
  { id: 'silver', name: 'Silver', transaction_value_min: 15000000 },
  { id: 'gold', name: 'Gold', transaction_value_min: 40000000 },
  { id: 'diamond', name: 'Diamond', transaction_value_min: 100000000 },
])
const isFetchingTiers = ref(false)
const editableRooms = ref([])
const isSavingRoomPlan = ref({})
const roomPlanSuccess = ref({})

// Bookings & Point release state
const bookings = ref([])
const isLoadingBookings = ref(false)
const isActionLoading = ref({})

const form = ref({
  name: '',
  tagline: '',
  city: '',
  country: '',
  image_url: '',
  description: '',
  point_release_mode: 'automated',
  membership: {
    client_id: '',
    client_secret: '',
    x_tenant_domain: 'jeevawasa.localhost',
    is_active: true,
  },
})

const fetchProperties = async () => {
  try {
    const res = await fetch(`${bffUrl}/api/extranet/properties`)
    const json = await res.json()
    if (json.success) {
      properties.value = json.data
      if (properties.value.length > 0) {
        selectProperty(properties.value[0])
      }
    }
  } catch (err) {
    errorMessage.value = 'Failed to load properties from Extranet API: ' + err.message
  }
}

const selectProperty = async (prop) => {
  selectedPropertyId.value = prop.id
  selectedProperty.value = prop
  testResult.value = null
  testLogoFailed.value = false
  successMessage.value = ''
  errorMessage.value = ''

  form.value = {
    name: prop.name,
    tagline: prop.tagline || '',
    city: prop.city,
    country: prop.country,
    image_url: prop.image_url || '',
    description: prop.description || '',
    point_release_mode: prop.point_release_mode || 'automated',
    membership: {
      client_id: prop.membership_property?.client_id || '',
      client_secret: prop.membership_property?.client_secret || '',
      x_tenant_domain: prop.membership_property?.x_tenant_domain || 'jeevawasa.localhost',
      is_active: prop.membership_property?.is_active ?? true,
    },
  }

  // Clone rooms for dynamic rate editing
  editableRooms.value = (prop.rooms || []).map((r) => ({
    ...r,
    tier_discount_rates: r.tier_discount_rates || {
      Bronze: 5,
      Silver: 10,
      Gold: 15,
      Diamond: 20,
    },
    is_member_rate_applicable: Boolean(r.is_member_rate_applicable),
  }))

  await syncTiers()

  if (activeSection.value === 'bookings') {
    await fetchBookings()
  }
}

// Sync real tiers from central Membership Platform
const syncTiers = async () => {
  if (!selectedPropertyId.value) return
  isFetchingTiers.value = true
  try {
    const res = await fetch(`${bffUrl}/api/extranet/properties/${selectedPropertyId.value}/tiers`)
    const json = await res.json()
    if (json.success && Array.isArray(json.data) && json.data.length > 0) {
      activeTiers.value = json.data
    }
  } catch (err) {
    console.warn('Failed to sync tiers from membership:', err)
  } finally {
    isFetchingTiers.value = false
  }
}

// Tier matrix helpers
const isTierActive = (room, tierName) => {
  if (!room.tier_discount_rates) return false
  const val = room.tier_discount_rates[tierName]
  return val !== undefined && val !== null && val > 0
}

const getTierRate = (room, tierName) => {
  if (!room.tier_discount_rates) return 0
  return room.tier_discount_rates[tierName] ?? 0
}

const toggleTierActive = (room, tierName, active) => {
  if (!room.tier_discount_rates) room.tier_discount_rates = {}
  if (active) {
    // Default tier defaults
    const defaults = { Bronze: 5, Silver: 10, Gold: 15, Diamond: 20 }
    room.tier_discount_rates[tierName] = defaults[tierName] || 10
  } else {
    delete room.tier_discount_rates[tierName]
  }
}

const updateTierRate = (room, tierName, val) => {
  if (!room.tier_discount_rates) room.tier_discount_rates = {}
  room.tier_discount_rates[tierName] = Math.max(0, Math.min(90, parseInt(val || '0', 10)))
}

const calculateTierPrice = (room, tierName) => {
  const base = parseFloat(room.base_price || 0)
  const percent = getTierRate(room, tierName)
  const discount = (base * percent) / 100
  return Math.max(0, base - discount)
}

const calculateTierSavings = (room, tierName) => {
  const base = parseFloat(room.base_price || 0)
  const percent = getTierRate(room, tierName)
  return Math.round((base * percent) / 100)
}

// Save room rate plan
const handleSaveRoomRatePlan = async (room) => {
  isSavingRoomPlan.value[room.id] = true
  roomPlanSuccess.value[room.id] = false
  errorMessage.value = ''

  try {
    const res = await fetch(`${bffUrl}/api/extranet/properties/${selectedPropertyId.value}/rooms/${room.id}/rate-plan`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        is_member_rate_applicable: room.is_member_rate_applicable,
        tier_discount_rates: room.tier_discount_rates || {},
      }),
    })

    const json = await res.json()
    if (!res.ok || !json.success) {
      throw new Error(json.message || 'Failed to update rate plan.')
    }

    roomPlanSuccess.value[room.id] = true
    setTimeout(() => {
      roomPlanSuccess.value[room.id] = false
    }, 3500)
  } catch (err) {
    errorMessage.value = `Error saving ${room.name}: ${err.message}`
  } finally {
    isSavingRoomPlan.value[room.id] = false
  }
}

// Test OAuth connection (Auto-resolves Merchant ID)
const handleTestConnection = async () => {
  isTestingConnection.value = true
  testResult.value = null
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const res = await fetch(`${bffUrl}/api/extranet/properties/${selectedPropertyId.value}/test-connection`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(form.value.membership),
    })

    const json = await res.json()
    if (!res.ok || !json.success) {
      throw new Error(json.message || 'Connection test failed.')
    }

    testResult.value = json.data
    successMessage.value = 'OAuth 2.1 Connection Verified & Property Context Resolved!'
    await syncTiers()
  } catch (err) {
    errorMessage.value = err.message
  } finally {
    isTestingConnection.value = false
  }
}

// Save Property & Integration settings
const handleSaveProperty = async () => {
  isSaving.value = true
  errorMessage.value = ''
  successMessage.value = ''

  try {
    const res = await fetch(`${bffUrl}/api/extranet/properties/${selectedPropertyId.value}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({
        ...form.value,
        has_membership: Boolean(form.value.membership.client_id && form.value.membership.client_id.trim() !== ''),
      }),
    })

    const json = await res.json()
    if (!res.ok || !json.success) {
      throw new Error(json.message || 'Failed to update property settings.')
    }

    successMessage.value = 'Property & Membership configuration saved successfully!'
    await fetchProperties()
  } catch (err) {
    errorMessage.value = err.message
  } finally {
    isSaving.value = false
  }
}

// Fetch bookings for the selected property
const fetchBookings = async () => {
  if (!selectedPropertyId.value) return
  isLoadingBookings.value = true
  errorMessage.value = ''
  try {
    const res = await fetch(`${bffUrl}/api/extranet/properties/${selectedPropertyId.value}/bookings`)
    const json = await res.json()
    if (json.success) {
      bookings.value = json.data || []
    }
  } catch (err) {
    errorMessage.value = 'Failed to load bookings: ' + err.message
  } finally {
    isLoadingBookings.value = false
  }
}

// Materialize pending points manually for a booking
const handleMaterializePoints = async (bookingId) => {
  isActionLoading.value[bookingId] = true
  errorMessage.value = ''
  successMessage.value = ''
  try {
    const res = await fetch(`${bffUrl}/api/extranet/properties/${selectedPropertyId.value}/bookings/${bookingId}/materialize`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
    })
    const json = await res.json()
    if (!res.ok || !json.success) {
      throw new Error(json.message || 'Failed to materialize points.')
    }
    successMessage.value = json.message || 'Points materialized successfully to member balance!'
    await fetchBookings()
  } catch (err) {
    errorMessage.value = err.message
  } finally {
    isActionLoading.value[bookingId] = false
  }
}

// Update booking status (e.g. CHECKED_IN or CHECKED_OUT) with auto-point release trigger
const handleUpdateStatus = async (bookingId, status) => {
  isActionLoading.value[bookingId] = true
  errorMessage.value = ''
  successMessage.value = ''
  try {
    const res = await fetch(`${bffUrl}/api/extranet/properties/${selectedPropertyId.value}/bookings/${bookingId}/status`, {
      method: 'PATCH',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify({ status }),
    })
    const json = await res.json()
    if (!res.ok || !json.success) {
      throw new Error(json.message || `Failed to update status to ${status}.`)
    }
    successMessage.value = json.message || `Booking status updated to ${status}.`
    await fetchBookings()
  } catch (err) {
    errorMessage.value = err.message
  } finally {
    isActionLoading.value[bookingId] = false
  }
}

const formatDateShort = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    })
  } catch {
    return dateStr
  }
}

const formatDateOnly = (dateStr) => {
  if (!dateStr) return '-'
  try {
    const d = new Date(dateStr)
    return d.toLocaleDateString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
    })
  } catch {
    return dateStr
  }
}

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(val || 0)
}

const formatShortCurrency = (val) => {
  if (!val || val === 0) return 'IDR 0'
  if (val >= 1000000) return `IDR ${(val / 1000000).toFixed(0)}M`
  return `IDR ${val}`
}

onMounted(() => {
  fetchProperties()
})
</script>

<style scoped>
.extranet-root {
  min-height: 100vh;
  background-color: #f8fafc;
  color: var(--colors-ink);
  display: flex;
  flex-direction: column;
}

/* Header */
.extranet-header {
  background: #0f172a;
  color: #ffffff;
  height: 70px;
  display: flex;
  align-items: center;
  border-bottom: 1px solid #1e293b;
}

.header-content {
  display: flex;
  align-items: center;
  justify-content: space-between;
  width: 100%;
}

.brand-group {
  display: flex;
  align-items: center;
  gap: 12px;
}

.hub-logo-wrap {
  display: flex;
  align-items: center;
  gap: 6px;
}

.hub-icon-svg {
  width: 20px;
  height: 20px;
  color: #ff385c;
}

.hub-logo {
  font-size: 20px;
  font-weight: 800;
  color: #ffffff;
  letter-spacing: -0.5px;
}

.extranet-badge {
  background: #334155;
  color: #38bdf8;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 4px;
  letter-spacing: 0.8px;
}

.sub-caption {
  font-size: 13px;
  color: #94a3b8;
}

.btn-public-engine {
  display: flex;
  align-items: center;
  gap: 8px;
  background: #1e293b;
  color: #f8fafc;
  padding: 8px 16px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 600;
  border: 1px solid #334155;
  transition: background-color 0.15s ease;
  text-decoration: none;
}

.btn-public-engine:hover {
  background: #334155;
}

.arrow-svg {
  width: 14px;
  height: 14px;
}

/* Main Layout */
.extranet-main {
  flex: 1;
  padding: 32px 24px;
}

.extranet-layout-grid {
  display: grid;
  grid-template-columns: 320px 1fr;
  gap: 28px;
}

/* Sidebar */
.property-sidebar {
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline-soft);
  border-radius: var(--radius-md);
  box-shadow: none;
  padding: 20px;
  height: fit-content;
}

.sidebar-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--colors-hairline-soft);
}

.sidebar-title {
  font-size: 15px;
  font-weight: 700;
}

.prop-count-tag {
  font-size: 11px;
  background: var(--colors-surface-soft);
  padding: 3px 8px;
  border-radius: var(--radius-full);
  color: var(--colors-muted);
}

.property-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.prop-list-card {
  display: flex;
  gap: 12px;
  padding: 10px;
  border-radius: var(--radius-sm);
  border: 1px solid var(--colors-hairline-soft);
  cursor: pointer;
  transition: all 0.15s ease;
}

.prop-list-card:hover {
  background-color: var(--colors-surface-soft);
}

.prop-list-card.active {
  border-color: var(--colors-primary);
  background-color: #fff1f2;
}

.prop-thumb-img {
  width: 52px;
  height: 52px;
  border-radius: var(--radius-sm);
  object-fit: cover;
}

.prop-card-body {
  flex: 1;
  min-width: 0;
}

.prop-card-name {
  font-size: 14px;
  font-weight: 600;
  color: var(--colors-ink);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.prop-card-city {
  font-size: 12px;
  color: var(--colors-muted);
  margin-bottom: 4px;
}

.status-indicator-pill {
  display: inline-flex;
  align-items: center;
  gap: 5px;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: var(--radius-full);
}

.status-connected {
  background: #dcfce7;
  color: #15803d;
}

.status-connected .dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #22c55e;
}

.status-disconnected {
  background: #f1f5f9;
  color: #64748b;
}

.status-disconnected .dot {
  width: 5px;
  height: 5px;
  border-radius: 50%;
  background: #94a3b8;
}

/* Editor Section */
.editor-section {
  display: flex;
  flex-direction: column;
}

.editor-header-banner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.editor-kicker {
  font-size: 11px;
  font-weight: 800;
  letter-spacing: 0.8px;
  color: var(--colors-primary);
  margin-bottom: 2px;
}

.editor-title {
  font-size: 26px;
  font-weight: 800;
  color: var(--colors-ink);
  letter-spacing: -0.4px;
}

.editor-subtitle {
  font-size: 13px;
  color: var(--colors-muted);
}

.btn-preview-property {
  display: flex;
  align-items: center;
  gap: 6px;
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline);
  color: var(--colors-ink);
  padding: 8px 14px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 600;
  text-decoration: none;
  box-shadow: none;
  transition: all 0.15s ease;
}

.btn-preview-property:hover {
  border-color: var(--colors-ink);
}

/* Alert Boxes */
.extranet-alert {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 12px 16px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 600;
  margin-bottom: 20px;
}

.alert-svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

.alert-success {
  background: #f0fdf4;
  color: #15803d;
  border: 1px solid #bbf7d0;
}

.alert-error {
  background: #fef2f2;
  color: #b91c1c;
  border: 1px solid #fecaca;
}

/* Tab Navigation Bar */
.editor-tabs-bar {
  display: flex;
  gap: 8px;
  border-bottom: 1px solid #e2e8f0;
  margin-bottom: 24px;
  padding-bottom: 0;
}

.editor-tab-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  background: transparent;
  border: none;
  border-bottom: 2px solid transparent;
  font-size: 13px;
  font-weight: 600;
  color: #64748b;
  cursor: pointer;
  transition: all 0.15s ease;
}

.editor-tab-btn svg {
  width: 16px;
  height: 16px;
}

.editor-tab-btn:hover {
  color: #0f172a;
}

.editor-tab-btn.active {
  color: #0f172a;
  border-bottom-color: #0f172a;
  font-weight: 700;
}

/* Config Cards */
.config-card {
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline-soft);
  border-radius: var(--radius-md);
  box-shadow: none;
  padding: 24px;
}

.card-title-row {
  display: flex;
  gap: 14px;
  margin-bottom: 20px;
}

.card-title-row.with-action {
  justify-content: space-between;
  align-items: center;
}

.card-title-left {
  display: flex;
  gap: 14px;
}

.card-icon-tag {
  width: 38px;
  height: 38px;
  border-radius: var(--radius-sm);
  background: var(--colors-surface-soft);
  color: #0f172a;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.card-icon-tag svg {
  width: 18px;
  height: 18px;
}

.card-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 2px;
}

.card-desc {
  font-size: 13px;
  color: var(--colors-muted);
  line-height: 1.4;
}

/* Auto resolution note */
.auto-resolution-info-box {
  display: flex;
  align-items: center;
  gap: 10px;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 8px;
  padding: 10px 14px;
  margin-bottom: 20px;
  font-size: 12px;
  color: #166534;
  line-height: 1.4;
}

.auto-resolution-info-box svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
}

/* Form Grid */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 18px;
}

.field-item.full-width {
  grid-column: span 2;
}

.form-label {
  display: block;
  font-size: 13px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 6px;
}

.extranet-input,
.extranet-textarea {
  width: 100%;
  padding: 10px 14px;
  border: 1px solid var(--colors-hairline);
  border-radius: var(--radius-sm);
  font-size: 14px;
  color: var(--colors-ink);
  outline: none;
  background: var(--colors-canvas);
  transition: border-color 0.15s ease;
  box-sizing: border-box;
}

.extranet-input:focus,
.extranet-textarea:focus {
  border-color: var(--colors-ink);
}

.extranet-input.monospace {
  font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
  font-size: 13px;
}

.field-hint {
  display: block;
  font-size: 11px;
  color: var(--colors-muted);
  margin-top: 4px;
}

/* Test Result Box */
.test-result-box {
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  border-radius: var(--radius-sm);
  padding: 16px;
  margin-top: 20px;
}

.test-result-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 12px;
}

.badge-success {
  background: #22c55e;
  color: white;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: var(--radius-full);
}

.test-time {
  font-size: 11px;
  color: var(--colors-muted);
}

.resolved-context-grid {
  display: flex;
  gap: 16px;
  align-items: center;
}

.resolved-logo {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-sm);
  object-fit: contain;
  background: white;
  border: 1px solid #e2e8f0;
  padding: 4px;
}

.resolved-logo-seal {
  width: 48px;
  height: 48px;
  border-radius: var(--radius-sm);
  background: #0f172a;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 14px;
}

.resolved-meta {
  display: flex;
  flex-direction: column;
  gap: 3px;
  font-size: 12px;
}

.res-id-chip {
  font-family: monospace;
  background: #e2e8f0;
  padding: 1px 6px;
  border-radius: 4px;
  font-size: 11px;
}

/* Connection Actions */
.connection-actions-row {
  display: flex;
  gap: 12px;
  margin-top: 24px;
  padding-top: 18px;
  border-top: 1px solid var(--colors-hairline-soft);
}

.btn-secondary {
  display: flex;
  align-items: center;
  gap: 8px;
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline);
  color: var(--colors-ink);
  padding: 10px 18px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: none;
  transition: all 0.15s ease;
}

.btn-secondary:hover:not(:disabled) {
  border-color: var(--colors-ink);
}

.btn-primary {
  background: var(--colors-ink);
  color: var(--colors-canvas);
  border: 1px solid var(--colors-ink);
  padding: 10px 20px;
  border-radius: var(--radius-sm);
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: none;
  transition: background 0.15s ease;
}

.btn-primary:hover:not(:disabled) {
  background: #262626;
}

.btn-svg {
  width: 14px;
  height: 14px;
}

/* TAB 2: Dynamic Room Rate Plans */
.btn-sync-tiers {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  color: #334155;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.btn-sync-tiers:hover:not(:disabled) {
  background: #e2e8f0;
}

.sync-svg {
  width: 14px;
  height: 14px;
}

.spin-anim {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

.tiers-strip-box {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  margin-bottom: 20px;
}

.tiers-strip-label {
  font-size: 12px;
  font-weight: 700;
  color: #475569;
}

.tiers-pills {
  display: flex;
  gap: 8px;
  flex-wrap: wrap;
}

.tier-chip {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 3px 8px;
  background: #ffffff;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-size: 11px;
}

.tier-chip-name {
  font-weight: 700;
  color: #0f172a;
}

.tier-chip-min {
  color: #64748b;
}

/* Room Plans List */
.room-plans-list {
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.room-plan-card {
  border: 1px solid #e2e8f0;
  border-radius: 10px;
  padding: 16px;
  background: #ffffff;
}

.room-plan-header {
  display: flex;
  align-items: center;
  gap: 14px;
  padding-bottom: 14px;
  border-bottom: 1px solid #f1f5f9;
}

.room-plan-thumb {
  width: 56px;
  height: 56px;
  border-radius: 6px;
  object-fit: cover;
  flex-shrink: 0;
}

.room-plan-meta {
  flex: 1;
}

.room-plan-name {
  margin: 0 0 4px;
  font-size: 15px;
  font-weight: 700;
  color: #0f172a;
}

.room-plan-sub {
  font-size: 12px;
  color: #64748b;
  display: flex;
  align-items: center;
  gap: 6px;
}

.dot-sep {
  color: #cbd5e1;
}

/* Toggle Switch */
.member-rate-toggle-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
}

.toggle-switch {
  position: relative;
  display: inline-block;
  width: 40px;
  height: 22px;
}

.toggle-switch input {
  opacity: 0;
  width: 0;
  height: 0;
}

.slider {
  position: absolute;
  cursor: pointer;
  inset: 0;
  background-color: #cbd5e1;
  transition: 0.2s;
  border-radius: 22px;
}

.slider:before {
  position: absolute;
  content: "";
  height: 16px;
  width: 16px;
  left: 3px;
  bottom: 3px;
  background-color: white;
  transition: 0.2s;
  border-radius: 50%;
}

input:checked + .slider {
  background-color: #0f172a;
}

input:checked + .slider:before {
  transform: translateX(18px);
}

.toggle-label {
  font-size: 12px;
  font-weight: 600;
  color: #334155;
}

/* Tier Matrix Table */
.tier-matrix-table-wrap {
  margin-top: 14px;
  overflow-x: auto;
}

.tier-matrix-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
}

.tier-matrix-table th {
  text-align: left;
  padding: 8px 10px;
  font-weight: 700;
  color: #475569;
  background: #f8fafc;
  border-bottom: 1px solid #e2e8f0;
}

.tier-matrix-table td {
  padding: 10px;
  border-bottom: 1px solid #f1f5f9;
  color: #1e293b;
}

.tier-name-cell {
  display: flex;
  align-items: center;
  gap: 6px;
}

.tier-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
  background: #f59e0b;
}

.tier-bold {
  font-weight: 700;
}

.tier-checkbox {
  width: 16px;
  height: 16px;
  cursor: pointer;
}

.percent-input-wrapper {
  display: inline-flex;
  align-items: center;
  border: 1px solid #cbd5e1;
  border-radius: 4px;
  overflow: hidden;
  background: #ffffff;
}

.percent-input {
  width: 44px;
  padding: 4px 6px;
  border: none;
  font-size: 12px;
  font-weight: 600;
  text-align: right;
  outline: none;
}

.percent-sign {
  padding: 0 6px 0 2px;
  font-size: 11px;
  color: #64748b;
}

.rate-preview-cell {
  font-weight: 700;
  color: #0f172a;
}

.savings-tag {
  background: #f0fdf4;
  color: #15803d;
  padding: 2px 6px;
  border-radius: 4px;
  font-weight: 600;
  font-size: 11px;
}

.text-muted {
  color: #94a3b8;
}

/* Room Plan Footer */
.room-plan-footer {
  display: flex;
  align-items: center;
  margin-top: 14px;
  padding-top: 10px;
}

.room-saved-msg {
  display: flex;
  align-items: center;
  gap: 4px;
  font-size: 12px;
  color: #15803d;
  font-weight: 600;
}

.check-svg {
  width: 14px;
  height: 14px;
}

.spacer {
  flex: 1;
}

.btn-save-room-plan {
  padding: 6px 14px;
  background: #0f172a;
  color: #ffffff;
  border: 1px solid #0f172a;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.btn-save-room-plan:hover:not(:disabled) {
  background: #334155;
}

/* Point Release Section */
.point-release-section {
  margin-top: 24px;
  padding-top: 20px;
  border-top: 1px solid #e2e8f0;
}

.section-divider-title {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 14px;
  font-weight: 700;
  color: #0f172a;
}

.section-divider-title svg {
  width: 18px;
  height: 18px;
  color: #0284c7;
  flex-shrink: 0;
}

.section-subtitle {
  font-size: 12px;
  color: #64748b;
  margin-top: 3px;
  margin-bottom: 12px;
}

.point-release-cards-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
  margin-top: 10px;
}

@media (max-width: 900px) {
  .point-release-cards-grid {
    grid-template-columns: 1fr;
  }
}

.release-mode-card {
  border: 1.5px solid #e2e8f0;
  border-radius: 10px;
  padding: 12px 14px;
  background: #ffffff;
  cursor: pointer;
  transition: all 0.15s ease;
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.release-mode-card:hover {
  border-color: #cbd5e1;
  background: #f8fafc;
}

.release-mode-card.active {
  border-color: #0284c7;
  background: #f0f9ff;
  box-shadow: 0 2px 8px rgba(2, 132, 199, 0.08);
}

.mode-card-top {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 8px;
}

.mode-radio-row {
  display: flex;
  align-items: center;
  gap: 8px;
  min-width: 0;
}

.mode-radio-row input[type="radio"] {
  cursor: pointer;
  accent-color: #0284c7;
  width: 15px;
  height: 15px;
  flex-shrink: 0;
  margin: 0;
}

.mode-title {
  font-size: 13px;
  font-weight: 700;
  color: #0f172a;
  cursor: pointer;
  white-space: nowrap;
}

.mode-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 7px;
  border-radius: 9999px;
  white-space: nowrap;
  flex-shrink: 0;
}

.badge-instant {
  background: #fef3c7;
  color: #92400e;
}

.badge-scheduled {
  background: #e0e7ff;
  color: #3730a3;
}

.badge-completion {
  background: #ecfdf5;
  color: #065f46;
}

.mode-desc {
  font-size: 11px;
  color: #64748b;
  line-height: 1.4;
  margin: 0;
  padding-left: 23px;
}

/* Policy Summary Banner in Reservations Tab */
.policy-summary-banner {
  display: flex;
  align-items: center;
  gap: 20px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 10px 16px;
  margin-bottom: 16px;
}

.policy-banner-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.banner-label {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
}

.banner-number {
  font-size: 14px;
  font-weight: 800;
  color: #0f172a;
}

.mode-chip {
  font-size: 11px;
  font-weight: 700;
  padding: 3px 8px;
  border-radius: 6px;
  white-space: nowrap;
}

.chip-automated {
  background: #fef3c7;
  color: #92400e;
  border: 1px solid #fde68a;
}

.chip-checkin {
  background: #e0e7ff;
  color: #3730a3;
  border: 1px solid #c7d2fe;
}

.chip-checkout {
  background: #ecfdf5;
  color: #065f46;
  border: 1px solid #a7f3d0;
}

/* Bookings Loading & Empty State */
.bookings-loading-state {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  padding: 36px 16px;
  color: #64748b;
  font-size: 13px;
}

.spinner-dot {
  width: 18px;
  height: 18px;
  border: 2px solid #cbd5e1;
  border-top-color: #0284c7;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.bookings-empty-state {
  text-align: center;
  padding: 36px 20px;
  color: #64748b;
}

.empty-svg {
  width: 36px;
  height: 36px;
  margin: 0 auto 10px;
  color: #94a3b8;
}

.empty-title {
  font-size: 15px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 4px;
}

.empty-desc {
  font-size: 12px;
  color: #64748b;
  margin: 0;
}

/* Bookings Table */
.bookings-table-wrapper {
  overflow-x: auto;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background: #ffffff;
}

.bookings-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 12px;
  text-align: left;
}

.bookings-table th {
  background: #f8fafc;
  color: #475569;
  font-weight: 700;
  font-size: 11px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  padding: 10px 14px;
  border-bottom: 1px solid #e2e8f0;
  white-space: nowrap;
}

.bookings-table td {
  padding: 11px 14px;
  border-bottom: 1px solid #f1f5f9;
  vertical-align: middle;
}

.bookings-table tr:hover td {
  background: #f8fafc;
}

/* Column specific widths & alignment */
.col-code {
  white-space: nowrap;
  min-width: 135px;
}

.col-guest {
  min-width: 170px;
}

.col-room {
  min-width: 180px;
}

.col-mode {
  white-space: nowrap;
  min-width: 105px;
}

.col-points {
  white-space: nowrap;
  min-width: 130px;
}

.col-status {
  white-space: nowrap;
  min-width: 100px;
}

.col-actions {
  white-space: nowrap;
  min-width: 140px;
}

.text-right {
  text-align: right;
}

.code-cell {
  display: flex;
  flex-direction: column;
  gap: 2px;
  white-space: nowrap;
}

.booking-ref-code {
  font-family: monospace;
  font-weight: 700;
  color: #0f172a;
  font-size: 12px;
  letter-spacing: -0.2px;
  white-space: nowrap;
}

.booking-created-at {
  font-size: 11px;
  color: #94a3b8;
  white-space: nowrap;
}

.guest-cell {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.guest-name-row {
  display: flex;
  align-items: center;
  gap: 6px;
  flex-wrap: nowrap;
}

.guest-name {
  font-weight: 600;
  color: #1e293b;
  font-size: 12px;
  white-space: nowrap;
}

.member-tag {
  background: #fdf2f8;
  color: #be185d;
  border: 1px solid #fbcfe8;
  font-size: 10px;
  font-weight: 700;
  padding: 1px 5px;
  border-radius: 4px;
  line-height: 1.2;
  white-space: nowrap;
}

.guest-tag {
  background: #f1f5f9;
  color: #64748b;
  font-size: 10px;
  font-weight: 600;
  padding: 1px 5px;
  border-radius: 4px;
  line-height: 1.2;
  white-space: nowrap;
}

.guest-email {
  font-size: 11px;
  color: #64748b;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 160px;
}

.room-date-cell {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.room-title {
  font-weight: 600;
  color: #1e293b;
  font-size: 12px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 180px;
}

.dates-range {
  font-size: 11px;
  color: #64748b;
  white-space: nowrap;
}

.mode-pill {
  font-size: 11px;
  font-weight: 600;
  padding: 3px 8px;
  border-radius: 6px;
  display: inline-block;
  white-space: nowrap;
}

.pill-auto {
  background: #fef3c7;
  color: #92400e;
}

.pill-checkin {
  background: #e0e7ff;
  color: #3730a3;
}

.pill-checkout {
  background: #ecfdf5;
  color: #065f46;
}

.points-cell {
  display: flex;
  flex-direction: column;
}

.points-row {
  display: flex;
  align-items: center;
  gap: 6px;
  white-space: nowrap;
}

.points-val {
  font-weight: 700;
  color: #0f172a;
  font-size: 12px;
  white-space: nowrap;
}

.materialize-badge {
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
  display: inline-block;
  white-space: nowrap;
}

.badge-done {
  background: #dcfce7;
  color: #15803d;
}

.badge-pending {
  background: #fef9c3;
  color: #854d0e;
}

.badge-none {
  color: #cbd5e1;
}

.status-chip {
  font-size: 10px;
  font-weight: 700;
  padding: 3px 7px;
  border-radius: 5px;
  text-transform: uppercase;
  display: inline-block;
  white-space: nowrap;
  letter-spacing: 0.3px;
}

.status-confirmed {
  background: #dbeafe;
  color: #1e40af;
}

.status-checked_in {
  background: #e0e7ff;
  color: #3730a3;
}

.status-checked_out {
  background: #f1f5f9;
  color: #475569;
}

.status-cancelled {
  background: #fee2e2;
  color: #991b1b;
}

.actions-cell {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  gap: 5px;
  white-space: nowrap;
}

.btn-action-sm {
  padding: 4px 9px;
  border-radius: 5px;
  font-size: 11px;
  font-weight: 700;
  border: 1px solid transparent;
  cursor: pointer;
  transition: all 0.15s ease;
  white-space: nowrap;
}

.btn-action-sm:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.btn-checkin {
  background: #e0e7ff;
  color: #3730a3;
  border-color: #c7d2fe;
}

.btn-checkin:hover:not(:disabled) {
  background: #c7d2fe;
}

.btn-checkout {
  background: #ecfdf5;
  color: #065f46;
  border-color: #a7f3d0;
}

.btn-checkout:hover:not(:disabled) {
  background: #a7f3d0;
}

.btn-release-point {
  background: #fef3c7;
  color: #92400e;
  border-color: #fde68a;
}

.btn-release-point:hover:not(:disabled) {
  background: #fde68a;
}
</style>
