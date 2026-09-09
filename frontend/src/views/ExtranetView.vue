<template>
  <div class="extranet-root">
    <!-- Top Extranet Bar -->
    <header class="extranet-header">
      <div class="container header-content">
        <div class="brand-group">
          <span class="hub-logo">⚡ stayhub</span>
          <span class="extranet-badge">EXTRANET</span>
          <span class="sub-caption">Channel & Membership PMS Gateway</span>
        </div>

        <div class="header-nav-actions">
          <router-link :to="`/book/${selectedPropertyId || 1}`" class="btn-public-engine">
            <span>View Public Booking Engine</span>
            <span class="arrow-icon">↗</span>
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

        <!-- Right Content: Property & OAuth Connection Editor -->
        <section v-if="selectedProperty" class="editor-section">
          <!-- Card Header -->
          <div class="editor-header-banner">
            <div>
              <div class="editor-kicker">PROPERTY & CHANNEL SETTINGS</div>
              <h2 class="editor-title">{{ selectedProperty.name }}</h2>
              <p class="editor-subtitle">{{ selectedProperty.tagline }} • {{ selectedProperty.city }}</p>
            </div>

            <router-link :to="`/book/${selectedProperty.id}`" class="btn-preview-property" target="_blank">
              <span>Open in Booking Engine</span>
              <span>↗</span>
            </router-link>
          </div>

          <!-- Alert Messages -->
          <div v-if="successMessage" class="extranet-alert alert-success">
            <span class="alert-icon">✓</span>
            <span>{{ successMessage }}</span>
          </div>

          <div v-if="errorMessage" class="extranet-alert alert-error">
            <span class="alert-icon">✕</span>
            <span>{{ errorMessage }}</span>
          </div>

          <!-- Section 1: Membership Integration (OAuth 2.1) -->
          <div class="config-card">
            <div class="card-title-row">
              <div class="card-icon-tag">🔐</div>
              <div>
                <h4 class="card-title">Membership Platform Connection (OAuth 2.1)</h4>
                <p class="card-desc">
                  Connect this property to the central Membership API to enable guest SSO, member tier pricing, and real-time transaction points accrual via Push V2.
                </p>
              </div>
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
                <span class="field-hint">Tenant identifier for database resolution</span>
              </div>

              <div class="field-item">
                <label class="form-label">OAuth 2.1 Client ID (AppConnection UUID)</label>
                <input
                  v-model="form.membership.client_id"
                  type="text"
                  placeholder="a0f17583-5f52-4a2c-bd5b-782e81f75052"
                  class="extranet-input monospace"
                />
              </div>

              <div class="field-item">
                <label class="form-label">OAuth 2.1 Client Secret</label>
                <input
                  v-model="form.membership.client_secret"
                  type="password"
                  placeholder="••••••••••••••••••••••••"
                  class="extranet-input monospace"
                />
                <span class="field-hint">Encrypted & stored strictly on the BFF layer</span>
              </div>

              <div class="field-item">
                <label class="form-label">Merchant UUID</label>
                <input
                  v-model="form.membership.merchant_id"
                  type="text"
                  placeholder="a0f1662d-8a3a-4970-b026-f7a0ae944f18"
                  class="extranet-input monospace"
                />
              </div>
            </div>

            <!-- Connection Test Result Banner -->
            <div v-if="testResult" class="test-result-box">
              <div class="test-result-header">
                <span class="badge-success">Connection Healthy</span>
                <span class="test-time">Live Verified</span>
              </div>

              <div class="resolved-context-grid">
                <img
                  v-if="testResult.context?.branding?.logo_url && !testLogoFailed"
                  :src="testResult.context.branding.logo_url"
                  alt=""
                  class="resolved-logo"
                  @error="testLogoFailed = true"
                />
                <div v-else class="resolved-logo-seal">
                  {{ (testResult.context?.corporate?.name || 'JW').slice(0, 2).toUpperCase() }}
                </div>
                <div class="resolved-meta">
                  <div class="res-line"><strong>Corporate:</strong> {{ testResult.context?.corporate?.name }}</div>
                  <div class="res-line"><strong>Branch:</strong> {{ testResult.context?.branch?.name }}</div>
                  <div class="res-line"><strong>Merchant:</strong> {{ testResult.context?.merchant?.name }}</div>
                  <div class="res-line">
                    <strong>Allowed Auth:</strong>
                    OTP Email: {{ testResult.context?.capabilities?.login_methods?.otp_email ? 'YES' : 'NO' }} •
                    Google SSO: {{ testResult.context?.capabilities?.login_methods?.google_sso ? 'YES' : 'NO' }}
                  </div>
                </div>
              </div>
            </div>

            <!-- Action buttons for OAuth Connection -->
            <div class="connection-actions-row">
              <button
                class="btn-secondary btn-test-conn"
                :disabled="isTestingConnection || !form.membership.client_id"
                @click="handleTestConnection"
              >
                <span v-if="isTestingConnection">Testing Connection...</span>
                <span v-else>⚡ Test OAuth 2.1 Connection</span>
              </button>

              <button
                class="btn-primary btn-save-conn"
                :disabled="isSaving"
                @click="handleSaveProperty"
              >
                <span v-if="isSaving">Saving Settings...</span>
                <span v-else>Save Property Configuration</span>
              </button>
            </div>
          </div>

          <!-- Section 2: Basic Property Information -->
          <div class="config-card" style="margin-top: 24px;">
            <div class="card-title-row">
              <div class="card-icon-tag">🏨</div>
              <div>
                <h4 class="card-title">Property Details & Metadata</h4>
                <p class="card-desc">Public hotel name, location, and imagery displayed on the booking engine.</p>
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
          </div>
        </section>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const bffUrl = import.meta.env.VITE_BFF_API_URL || 'http://localhost:8002'

const properties = ref([])
const selectedPropertyId = ref(1)
const selectedProperty = ref(null)

const isTestingConnection = ref(false)
const isSaving = ref(false)
const testResult = ref(null)
const testLogoFailed = ref(false)
const successMessage = ref('')
const errorMessage = ref('')

const form = ref({
  name: '',
  tagline: '',
  city: '',
  country: '',
  image_url: '',
  description: '',
  membership: {
    client_id: '',
    client_secret: '',
    merchant_id: '',
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

const selectProperty = (prop) => {
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
    membership: {
      client_id: prop.membership_property?.client_id || '',
      client_secret: prop.membership_property?.client_secret || '',
      merchant_id: prop.membership_property?.merchant_id || '',
      x_tenant_domain: prop.membership_property?.x_tenant_domain || 'jeevawasa.localhost',
      is_active: prop.membership_property?.is_active ?? true,
    },
  }
}

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
  } catch (err) {
    errorMessage.value = err.message
  } finally {
    isTestingConnection.value = false
  }
}

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
        has_membership: !emptyOrNull(form.value.membership.client_id),
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

const emptyOrNull = (val) => !val || val.trim() === ''

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

.hub-logo {
  font-size: 20px;
  font-weight: 800;
  color: var(--colors-primary);
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
}

.btn-public-engine:hover {
  background: #334155;
}

.arrow-icon {
  font-size: 14px;
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
  box-shadow: var(--shadow-airbnb);
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
  font-weight: 700;
  letter-spacing: 0.8px;
  color: var(--colors-muted);
  margin-bottom: 4px;
}

.editor-title {
  font-size: 24px;
  font-weight: 700;
  color: var(--colors-ink);
}

.editor-subtitle {
  font-size: 13px;
  color: var(--colors-muted);
}

.btn-preview-property {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 8px 14px;
  border-radius: var(--radius-sm);
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline);
  font-size: 13px;
  font-weight: 600;
  color: var(--colors-ink);
  transition: all 0.15s ease;
}

.btn-preview-property:hover {
  background: var(--colors-surface-soft);
}

/* Cards */
.config-card {
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline-soft);
  border-radius: var(--radius-md);
  box-shadow: var(--shadow-airbnb);
  padding: 24px;
}

.card-title-row {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 20px;
}

.card-icon-tag {
  font-size: 24px;
}

.card-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 4px;
}

.card-desc {
  font-size: 13px;
  color: var(--colors-muted);
  line-height: 1.4;
}

/* Forms */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
  margin-bottom: 20px;
}

.field-item {
  display: flex;
  flex-direction: column;
}

.field-item.full-width {
  grid-column: 1 / -1;
}

.form-label {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--colors-ink);
  margin-bottom: 6px;
}

.extranet-input,
.extranet-textarea {
  border: 1px solid var(--colors-hairline);
  border-radius: var(--radius-sm);
  padding: 10px 14px;
  font-size: 14px;
  color: var(--colors-ink);
  outline: none;
  background: var(--colors-canvas);
  transition: border-color 0.2s ease;
}

.extranet-input:focus,
.extranet-textarea:focus {
  border-color: var(--colors-ink);
  border-width: 2px;
}

.monospace {
  font-family: monospace;
  font-size: 13px;
}

.field-hint {
  font-size: 11px;
  color: var(--colors-muted);
  margin-top: 4px;
}

/* Test Result Banner */
.test-result-box {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: var(--radius-sm);
  padding: 16px;
  margin-bottom: 20px;
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
  border-radius: 4px;
}

.test-time {
  font-size: 11px;
  color: #166534;
}

.resolved-context-grid {
  display: flex;
  align-items: center;
  gap: 16px;
}

.resolved-logo {
  height: 48px;
  width: auto;
  border-radius: 4px;
}

.resolved-logo-seal {
  width: 46px;
  height: 46px;
  border-radius: 8px;
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  color: #f59e0b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 15px;
  letter-spacing: 0.8px;
  border: 1px solid rgba(245, 158, 11, 0.35);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
  flex-shrink: 0;
}

.resolved-meta {
  font-size: 12px;
  color: #14532d;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

/* Action Buttons */
.connection-actions-row {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 16px;
  border-top: 1px solid var(--colors-hairline-soft);
}

.btn-test-conn {
  padding: 10px 18px;
  font-size: 13px;
}

.btn-save-conn {
  padding: 10px 22px;
  font-size: 13px;
}

/* Alerts */
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

.alert-success {
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #15803d;
}

.alert-error {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: var(--colors-error);
}

.alert-icon {
  font-size: 16px;
}

@media (max-width: 900px) {
  .extranet-layout-grid {
    grid-template-columns: 1fr;
  }
}
</style>
