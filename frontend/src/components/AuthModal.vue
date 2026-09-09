<template>
  <div v-if="isAuthModalOpen" class="modal-scrim" @click.self="closeAuthModal">
    <div ref="modalCardRef" class="airbnb-auth-card">
      <!-- Modal Top Header with Dynamic Property Context Branding -->
      <div class="auth-header">
        <div class="header-brand-box">
          <img
            v-if="resolvedLogo && !logoFailed"
            :src="resolvedLogo"
            :alt="resolvedMerchantName"
            class="merchant-brand-logo"
            @error="logoFailed = true"
          />
          <div v-else class="fallback-brand-icon">
            <div class="brand-avatar-seal">
              {{ resolvedCorporateName.slice(0, 2).toUpperCase() }}
            </div>
          </div>

          <div class="brand-text-block">
            <div class="brand-corp-label">{{ resolvedCorporateName }}</div>
            <div class="brand-prop-name">{{ resolvedMerchantName }}</div>
          </div>
        </div>

        <button class="close-btn" @click="closeAuthModal" title="Close">✕</button>
      </div>

      <div class="auth-body">
        <!-- Error Alert -->
        <div v-if="authError" class="auth-alert-error">
          <svg viewBox="0 0 24 24" fill="currentColor" class="alert-svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
          </svg>
          <div class="error-msg-wrap">
            <span class="error-text">{{ authError }}</span>
            <span v-if="authError.includes('INVALID_CREDENTIALS')" class="error-hint">
              Tip: If you don't recall your password, click <strong>Login with OTP</strong> below for instant access.
            </span>
          </div>
        </div>

        <!-- ============================================================== -->
        <!-- VIEW 1: UNIFIED SIGN IN (Email + Password + OTP link + Google) -->
        <!-- ============================================================== -->
        <div v-if="viewMode === 'login'" class="auth-view-content">
          <div class="title-section">
            <h3 class="auth-title">Sign In to Jeevawasa Club</h3>
            <p class="auth-subtitle">Unlock member rates and instant reward privileges.</p>
          </div>

          <form @submit.prevent="handlePasswordLogin" class="form-unified">
            <!-- Email Input -->
            <div class="input-field-wrap">
              <span class="input-icon">✉</span>
              <input
                v-model="loginEmail"
                type="email"
                required
                placeholder="Email address"
                class="airbnb-input"
              />
            </div>

            <!-- Password Input with Toggle -->
            <div class="input-field-wrap">
              <span class="input-icon">🔒</span>
              <input
                v-model="loginPassword"
                :type="showPassword ? 'text' : 'password'"
                required
                placeholder="Password"
                class="airbnb-input"
              />
              <button
                type="button"
                class="btn-toggle-eye"
                @click="showPassword = !showPassword"
                :title="showPassword ? 'Hide password' : 'Show password'"
              >
                {{ showPassword ? '🙈' : '👁️' }}
              </button>
            </div>

            <!-- Sub-links: Forgot Password & Login with OTP -->
            <div class="auth-sublinks-row">
              <a href="#" class="text-sublink" @click.prevent="switchToOtp">
                Forgot password?
              </a>
              <a href="#" class="text-sublink font-bold text-rausch" @click.prevent="switchToOtp">
                Login with OTP &rarr;
              </a>
            </div>

            <!-- Main Submit Button -->
            <button
              type="submit"
              class="btn-primary btn-submit-airbnb"
              :disabled="isLoading"
            >
              <span v-if="isLoading">Signing in...</span>
              <span v-else>Log In</span>
            </button>
          </form>

          <!-- Divider -->
          <div class="divider-with-text">
            <span>or</span>
          </div>

          <!-- Google SSO Container -->
          <div class="sso-block">
            <!-- Official Google Identity Services Mount Element -->
            <div id="google-btn-mount" class="google-mount-wrapper"></div>

            <!-- Native Airbnb Style Google Button -->
            <button
              type="button"
              class="btn-google-sso"
              :disabled="isLoading"
              @click="triggerGoogleLogin"
            >
              <svg class="google-svg" viewBox="0 0 48 48" width="18" height="18">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.15 1.45-4.92 2.3-8.16 2.3-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
              </svg>
              <span>Continue with Google</span>
            </button>

            <!-- Quick Demo Member Simulation -->
            <div class="demo-sso-strip">
              <span class="demo-label">Dev 1-Click SSO:</span>
              <button
                type="button"
                class="pill-quick-member"
                @click="handleQuickGoogleLogin('madewedaoffice@gmail.com')"
              >
                Made Weda
              </button>
              <button
                type="button"
                class="pill-quick-member"
                @click="handleQuickGoogleLogin('putuadityasatriawan@gmail.com')"
              >
                Aditya
              </button>
            </div>
          </div>

          <!-- Bottom Switch to Register -->
          <div class="auth-bottom-switch">
            <span>Don't have an account?</span>
            <a href="#" class="text-rausch-link" @click.prevent="switchToRegister">
              Sign Up (20% Off)
            </a>
          </div>
        </div>

        <!-- ============================================================== -->
        <!-- VIEW 2: PASSWORDLESS OTP MODE                                  -->
        <!-- ============================================================== -->
        <div v-else-if="viewMode === 'otp'" class="auth-view-content">
          <div class="title-section">
            <h3 class="auth-title">Login with OTP</h3>
            <p class="auth-subtitle">
              {{ otpRequested ? `Enter the 6-digit code sent to ${otpEmail}` : 'Enter your email to receive a magic sign-in code.' }}
            </p>
          </div>

          <!-- Step A: Enter Email -->
          <form v-if="!otpRequested" @submit.prevent="handleRequestOtp" class="form-unified">
            <div class="input-field-wrap">
              <span class="input-icon">✉</span>
              <input
                v-model="otpEmail"
                type="email"
                required
                placeholder="Registered member email"
                class="airbnb-input"
              />
            </div>

            <div class="quick-preset-row">
              <span class="preset-label">Quick fill:</span>
              <button
                type="button"
                class="preset-pill"
                @click="otpEmail = 'putuadityasatriawan@gmail.com'"
              >
                putuadityasatriawan@gmail.com
              </button>
            </div>

            <button
              type="submit"
              class="btn-primary btn-submit-airbnb"
              :disabled="isLoading"
            >
              <span v-if="isLoading">Sending Code...</span>
              <span v-else>Send Magic Code</span>
            </button>
          </form>

          <!-- Step B: Enter 6-digit OTP -->
          <form v-else @submit.prevent="handleVerifyOtp" class="form-unified">
            <div class="otp-input-center-box">
              <input
                v-model="otpCode"
                type="text"
                maxlength="6"
                placeholder="000000"
                required
                class="airbnb-otp-digits"
                autofocus
              />
            </div>

            <p class="otp-hint-msg">
              *Check your mailbox or local Mailpit (<strong>http://localhost:8025</strong>).
            </p>

            <div class="otp-timer-row">
              <span v-if="countdown > 0" class="countdown-label">
                Code expires in <strong>{{ formatTimer(countdown) }}</strong>
              </span>
              <button
                v-else
                type="button"
                class="btn-resend-otp"
                @click="handleRequestOtp"
              >
                Resend Code
              </button>
            </div>

            <button
              type="submit"
              class="btn-primary btn-submit-airbnb"
              :disabled="isLoading || otpCode.length < 6"
            >
              <span v-if="isLoading">Verifying...</span>
              <span v-else>Submit OTP & Sign In</span>
            </button>
          </form>

          <!-- Back to password login link -->
          <div class="auth-bottom-switch">
            <a href="#" class="text-sublink" @click.prevent="switchToLogin">
              &larr; Back to Email & Password Login
            </a>
          </div>
        </div>

        <!-- ============================================================== -->
        <!-- VIEW 3: REGISTER NEW MEMBER                                    -->
        <!-- ============================================================== -->
        <div v-else-if="viewMode === 'register'" class="auth-view-content">
          <div class="title-section">
            <h3 class="auth-title">Join Jeevawasa Club</h3>
            <p class="auth-subtitle">Enjoy 20% off all room suites and collect club points.</p>
          </div>

          <form @submit.prevent="handleRegisterSubmit" class="form-unified">
            <!-- Name Row -->
            <div class="two-col-grid">
              <div class="input-field-wrap">
                <input
                  v-model="regFirstName"
                  type="text"
                  required
                  placeholder="First Name"
                  class="airbnb-input"
                />
              </div>
              <div class="input-field-wrap">
                <input
                  v-model="regLastName"
                  type="text"
                  required
                  placeholder="Last Name"
                  class="airbnb-input"
                />
              </div>
            </div>

            <!-- Email -->
            <div class="input-field-wrap">
              <span class="input-icon">✉</span>
              <input
                v-model="regEmail"
                type="email"
                required
                placeholder="Email address"
                class="airbnb-input"
              />
            </div>

            <!-- Phone -->
            <div class="input-field-wrap">
              <span class="input-icon">📱</span>
              <input
                v-model="regPhone"
                type="tel"
                required
                placeholder="WhatsApp Phone Number"
                class="airbnb-input"
              />
            </div>

            <!-- Password -->
            <div class="input-field-wrap">
              <span class="input-icon">🔒</span>
              <input
                v-model="regPassword"
                type="password"
                required
                placeholder="Create Password (min 6 chars)"
                class="airbnb-input"
              />
            </div>

            <!-- Autofill Demo button -->
            <div class="quick-preset-row">
              <span class="preset-label">Demo:</span>
              <button
                type="button"
                class="preset-pill"
                @click="autofillDemoRegistration"
              >
                ⚡ Autofill Sample Member
              </button>
            </div>

            <button
              type="submit"
              class="btn-primary btn-submit-airbnb"
              :disabled="isLoading"
            >
              <span v-if="isLoading">Creating Account...</span>
              <span v-else>Join Jeevawasa Club</span>
            </button>
          </form>

          <div class="auth-bottom-switch">
            <span>Already have an account?</span>
            <a href="#" class="text-rausch-link" @click.prevent="switchToLogin">
              Log In
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, nextTick } from 'vue'
import gsap from 'gsap'
import { useAuth } from '../composables/useAuth'
import { useBooking } from '../composables/useBooking'

const GOOGLE_CLIENT_ID = '659157255403-l6fikllu69q2h18bc6e33rn0rekuogd7.apps.googleusercontent.com'

const {
  isAuthModalOpen,
  closeAuthModal,
  propertyContext,
  fetchPropertyContext,
  loginWithPassword,
  requestOtp,
  verifyOtp,
  loginWithGoogle,
  registerMember,
  isLoading,
  authError,
} = useAuth()

const { activePropertyId, activeProperty } = useBooking()

const modalCardRef = ref(null)
const viewMode = ref('login') // 'login' | 'otp' | 'register'
const logoFailed = ref(false)

// Login Form States
const loginEmail = ref('madewedaoffice@gmail.com')
const loginPassword = ref('')
const showPassword = ref(false)

// OTP Form States
const otpEmail = ref('putuadityasatriawan@gmail.com')
const otpCode = ref('')
const otpRequested = ref(false)
const countdown = ref(120)
let timerInterval = null

// Register Form States
const regFirstName = ref('Aditya')
const regLastName = ref('Satriawan')
const regEmail = ref('')
const regPhone = ref('+6281234567890')
const regPassword = ref('Passw0rd123!')

// Lifecycle & Watchers
onMounted(() => {
  if (activePropertyId.value) {
    fetchPropertyContext(activePropertyId.value)
  }
  initGoogleAuth()
})

watch(activePropertyId, (newId) => {
  logoFailed.value = false
  if (newId) {
    fetchPropertyContext(newId)
  }
})

watch(isAuthModalOpen, (isOpen) => {
  if (isOpen) {
    authError.value = ''
    viewMode.value = 'login'
    otpRequested.value = false
    logoFailed.value = false
    nextTick(() => {
      animateModalOpen()
      initGoogleAuth()
    })
  } else {
    clearInterval(timerInterval)
  }
})

const resolvedLogo = computed(() => {
  return propertyContext.value?.branding?.logo_url || activeProperty.value?.image_url
})

const resolvedCorporateName = computed(() => {
  return propertyContext.value?.corporate?.name || 'JEEVAWASA'
})

const resolvedMerchantName = computed(() => {
  return propertyContext.value?.branch?.name || activeProperty.value?.name || 'Luxury Resort'
})

// GSAP Animations
const animateModalOpen = () => {
  if (modalCardRef.value) {
    gsap.fromTo(
      modalCardRef.value,
      { y: 24, opacity: 0, scale: 0.98 },
      { y: 0, opacity: 1, scale: 1, duration: 0.3, ease: 'power2.out' }
    )
  }
}

const animateViewSwitch = () => {
  nextTick(() => {
    const el = document.querySelector('.auth-view-content')
    if (el) {
      gsap.fromTo(
        el,
        { opacity: 0, y: 10 },
        { opacity: 1, y: 0, duration: 0.22, ease: 'power1.out' }
      )
    }
  })
}

// Mode Switchers
const switchToLogin = () => {
  viewMode.value = 'login'
  authError.value = ''
  animateViewSwitch()
  nextTick(() => initGoogleAuth())
}

const switchToOtp = () => {
  viewMode.value = 'otp'
  authError.value = ''
  otpRequested.value = false
  otpCode.value = ''
  animateViewSwitch()
}

const switchToRegister = () => {
  viewMode.value = 'register'
  authError.value = ''
  animateViewSwitch()
}

// Google Identity Services (GIS)
const initGoogleAuth = () => {
  if (typeof window !== 'undefined' && window.google?.accounts?.id) {
    try {
      window.google.accounts.id.initialize({
        client_id: GOOGLE_CLIENT_ID,
        callback: handleGoogleCredentialResponse,
        auto_select: false,
      })

      const mountEl = document.getElementById('google-btn-mount')
      if (mountEl) {
        window.google.accounts.id.renderButton(mountEl, {
          theme: 'outline',
          size: 'large',
          width: 380,
          text: 'continue_with',
          shape: 'rectangular',
        })
      }
    } catch (err) {
      console.warn('Google GSI notice:', err.message)
    }
  }
}

const handleGoogleCredentialResponse = async (res) => {
  if (res?.credential) {
    try {
      await loginWithGoogle(activePropertyId.value, res.credential)
    } catch (err) {
      authError.value = err.message
    }
  }
}

const triggerGoogleLogin = () => {
  if (window.google?.accounts?.id) {
    window.google.accounts.id.prompt((notification) => {
      if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
        const reason = notification.getNotDisplayedReason?.() || notification.getSkippedReason?.() || 'origin_or_cookie_blocked'
        console.warn('Google GSI prompt blocked or skipped:', reason)
        authError.value = 'Origin http://localhost:5173 belum terdaftar di Authorized JavaScript Origins Google Cloud Console. Silakan gunakan tombol Dev 1-Click SSO di bawah untuk simulasi login instan.'
      }
    })
  } else {
    handleQuickGoogleLogin('madewedaoffice@gmail.com')
  }
}

const handleQuickGoogleLogin = async (email) => {
  try {
    await loginWithGoogle(activePropertyId.value, email)
  } catch (err) {
    authError.value = err.message
  }
}

// Password Login
const handlePasswordLogin = async () => {
  try {
    await loginWithPassword(activePropertyId.value, loginEmail.value, loginPassword.value)
  } catch (err) {
    // Handled in authError
  }
}

// OTP Flow
const handleRequestOtp = async () => {
  try {
    await requestOtp(activePropertyId.value, otpEmail.value)
    otpRequested.value = true
    countdown.value = 120
    clearInterval(timerInterval)
    timerInterval = setInterval(() => {
      if (countdown.value > 0) {
        countdown.value--
      } else {
        clearInterval(timerInterval)
      }
    }, 1000)
    animateViewSwitch()
  } catch (err) {
    // Handled in authError
  }
}

const handleVerifyOtp = async () => {
  try {
    await verifyOtp(activePropertyId.value, otpEmail.value, otpCode.value)
    clearInterval(timerInterval)
  } catch (err) {
    // Handled in authError
  }
}

const formatTimer = (sec) => {
  const m = Math.floor(sec / 60)
  const s = sec % 60
  return `${m}:${s < 10 ? '0' : ''}${s}`
}

// Registration
const autofillDemoRegistration = () => {
  const randomSuffix = Math.floor(1000 + Math.random() * 9000)
  regFirstName.value = 'Aditya'
  regLastName.value = 'Satriawan'
  regEmail.value = `aditya.guest.${randomSuffix}@example.com`
  regPhone.value = '+6281234567890'
  regPassword.value = 'Passw0rd123!'
}

const handleRegisterSubmit = async () => {
  if (!regEmail.value) {
    autofillDemoRegistration()
  }

  try {
    await registerMember(activePropertyId.value, {
      first_name: regFirstName.value,
      last_name: regLastName.value,
      email: regEmail.value,
      phone: regPhone.value,
      password: regPassword.value,
      registration_token: 'bypass_token_' + Date.now(),
    })
  } catch (err) {
    authError.value = 'Registration requires email verification. Please use OTP or Google SSO.'
  }
}
</script>

<style scoped>
.modal-scrim {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(2px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  padding: 16px;
}

.airbnb-auth-card {
  width: 100%;
  max-width: 440px;
  background: var(--colors-canvas);
  border-radius: 16px;
  border: 1px solid var(--colors-hairline);
  box-shadow: none;
  overflow: hidden;
}

/* Header */
.auth-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid var(--colors-hairline-soft);
}

.header-brand-box {
  display: flex;
  align-items: center;
  gap: 10px;
}

.merchant-brand-logo {
  height: 36px;
  width: auto;
  max-width: 100px;
  object-fit: contain;
  border-radius: 4px;
}

.fallback-brand-icon {
  display: flex;
  align-items: center;
  justify-content: center;
}

.brand-avatar-seal {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
  color: #f59e0b;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: 800;
  font-size: 13px;
  letter-spacing: 0.6px;
  border: 1px solid rgba(245, 158, 11, 0.3);
}

.brand-corp-label {
  font-size: 10px;
  font-weight: 800;
  letter-spacing: 0.8px;
  text-transform: uppercase;
  color: var(--colors-muted);
}

.brand-prop-name {
  font-size: 14px;
  font-weight: 700;
  color: var(--colors-ink);
}

.close-btn {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 14px;
  color: var(--colors-ink);
  transition: background-color 0.15s ease;
}

.close-btn:hover {
  background-color: var(--colors-surface-soft);
}

/* Body */
.auth-body {
  padding: 24px;
}

.title-section {
  margin-bottom: 20px;
}

.auth-title {
  font-size: 20px;
  font-weight: 700;
  color: var(--colors-ink);
  letter-spacing: -0.3px;
  margin-bottom: 4px;
}

.auth-subtitle {
  font-size: 13px;
  color: var(--colors-body);
}

/* Error Alert */
.auth-alert-error {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: #fff1f2;
  border: 1px solid rgba(225, 29, 72, 0.2);
  border-radius: 8px;
  padding: 10px 12px;
  margin-bottom: 16px;
  font-size: 12px;
  color: #be123c;
}

.alert-svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
  margin-top: 1px;
}

.error-msg-wrap {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.error-hint {
  font-size: 11px;
  color: var(--colors-body);
}

/* Form Styles */
.form-unified {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.input-field-wrap {
  position: relative;
  display: flex;
  align-items: center;
}

.input-icon {
  position: absolute;
  left: 12px;
  font-size: 14px;
  color: var(--colors-muted);
  pointer-events: none;
}

.airbnb-input {
  width: 100%;
  height: 44px;
  border: 1px solid var(--colors-hairline);
  border-radius: 8px;
  padding: 0 12px 0 36px;
  font-size: 14px;
  color: var(--colors-ink);
  outline: none;
  background: var(--colors-canvas);
  transition: border-color 0.15s ease;
}

.airbnb-input:focus {
  border-color: var(--colors-ink);
  border-width: 1.5px;
}

.btn-toggle-eye {
  position: absolute;
  right: 12px;
  font-size: 14px;
  color: var(--colors-muted);
  background: none;
  border: none;
  cursor: pointer;
}

.two-col-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.two-col-grid .airbnb-input {
  padding-left: 12px;
}

.auth-sublinks-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 12px;
  margin-top: -2px;
  margin-bottom: 4px;
}

.text-sublink {
  color: var(--colors-body);
  text-decoration: none;
}

.text-sublink:hover {
  text-decoration: underline;
}

.text-rausch {
  color: #ff385c;
}

.btn-submit-airbnb {
  width: 100%;
  height: 44px;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  border: none;
  margin-top: 4px;
  transition: opacity 0.15s ease, transform 0.1s ease;
}

.btn-submit-airbnb:active {
  transform: scale(0.99);
}

.btn-submit-airbnb:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Divider */
.divider-with-text {
  display: flex;
  align-items: center;
  text-align: center;
  margin: 18px 0;
  color: var(--colors-muted);
  font-size: 12px;
}

.divider-with-text::before,
.divider-with-text::after {
  content: '';
  flex: 1;
  border-bottom: 1px solid var(--colors-hairline-soft);
}

.divider-with-text span {
  padding: 0 10px;
}

/* SSO Block */
.sso-block {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.btn-google-sso {
  width: 100%;
  height: 44px;
  border-radius: 8px;
  border: 1px solid var(--colors-hairline);
  background: var(--colors-canvas);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  font-size: 14px;
  font-weight: 600;
  color: var(--colors-ink);
  cursor: pointer;
  transition: background-color 0.15s ease, border-color 0.15s ease;
}

.btn-google-sso:hover {
  background-color: var(--colors-surface-soft);
  border-color: var(--colors-ink);
}

.google-mount-wrapper {
  display: none; /* Auto-unhidden if GSI renders successfully */
  width: 100%;
  justify-content: center;
}

.demo-sso-strip {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: var(--colors-muted);
  margin-top: 2px;
  justify-content: center;
}

.demo-label {
  font-size: 11px;
}

.pill-quick-member {
  font-size: 11px;
  background: var(--colors-surface-soft);
  border: 1px solid var(--colors-hairline-soft);
  padding: 2px 8px;
  border-radius: 12px;
  color: var(--colors-body);
  cursor: pointer;
  transition: all 0.15s ease;
}

.pill-quick-member:hover {
  border-color: #ff385c;
  color: #ff385c;
  background: #fff1f2;
}

/* OTP Specific Styles */
.otp-input-center-box {
  display: flex;
  justify-content: center;
  margin: 10px 0;
}

.airbnb-otp-digits {
  width: 220px;
  height: 52px;
  text-align: center;
  font-size: 26px;
  font-weight: 800;
  letter-spacing: 12px;
  border: 1.5px solid var(--colors-hairline);
  border-radius: 10px;
  outline: none;
  color: var(--colors-ink);
}

.airbnb-otp-digits:focus {
  border-color: #ff385c;
}

.otp-hint-msg {
  font-size: 11px;
  color: var(--colors-muted);
  text-align: center;
  line-height: 1.4;
}

.otp-timer-row {
  display: flex;
  justify-content: center;
  font-size: 12px;
  color: var(--colors-body);
  margin: 4px 0 8px;
}

.btn-resend-otp {
  background: none;
  border: none;
  color: #ff385c;
  font-weight: 700;
  cursor: pointer;
  text-decoration: underline;
}

.quick-preset-row {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  color: var(--colors-muted);
}

.preset-pill {
  font-size: 11px;
  background: var(--colors-surface-soft);
  border: 1px solid var(--colors-hairline-soft);
  border-radius: 4px;
  padding: 2px 6px;
  cursor: pointer;
  color: var(--colors-body);
}

.preset-pill:hover {
  border-color: #ff385c;
  color: #ff385c;
}

/* Bottom Switch */
.auth-bottom-switch {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  margin-top: 20px;
  font-size: 13px;
  color: var(--colors-body);
  border-top: 1px solid var(--colors-hairline-soft);
  padding-top: 16px;
}

.text-rausch-link {
  color: #ff385c;
  font-weight: 700;
  text-decoration: none;
}

.text-rausch-link:hover {
  text-decoration: underline;
}

@media (max-width: 480px) {
  .airbnb-auth-card {
    max-width: 100%;
    border-radius: 12px;
  }
  .auth-body {
    padding: 18px;
  }
}
</style>
