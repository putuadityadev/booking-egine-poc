<template>
  <div v-if="isAuthModalOpen" class="modal-scrim" @click.self="closeAuthModal">
    <div ref="modalCardRef" class="airbnb-auth-card">
      <!-- Modal Top Header with Dynamic Property Context Branding -->
      <div class="auth-header">
        <div class="header-brand-box">
          <img
            :src="resolvedLogo"
            :alt="resolvedMerchantName"
            class="merchant-brand-logo"
          />

          <div class="brand-text-block">
            <div class="brand-corp-label">{{ resolvedCorporateName }}</div>
            <div class="brand-prop-name">{{ resolvedMerchantName }}</div>
          </div>
        </div>

        <button class="close-btn" @click="closeAuthModal" title="Close" aria-label="Close auth dialog">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="close-dialog-svg">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>

      <div class="auth-body">
        <!-- Alert (Error or Pending Approval Notice) -->
        <div v-if="authError" :class="authError.toLowerCase().includes('pending approval') ? 'auth-alert-warning' : 'auth-alert-error'">
          <svg v-if="authError.toLowerCase().includes('pending approval')" viewBox="0 0 24 24" fill="currentColor" class="alert-svg">
            <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.2 3.1.8-1.2-4.5-2.7V7z"/>
          </svg>
          <svg v-else viewBox="0 0 24 24" fill="currentColor" class="alert-svg">
            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
          </svg>
          <div class="error-msg-wrap">
            <span class="error-text">{{ authError }}</span>
            <span v-if="authError.includes('INVALID_CREDENTIALS')" class="error-hint">
              Tip: If you don't recall your password, click <strong>Login with OTP</strong> below for instant access.
            </span>
            <span v-else-if="authError.toLowerCase().includes('already registered') || authError.toLowerCase().includes('already exists')" class="error-hint">
              Tip: Click <strong style="cursor: pointer; text-decoration: underline;" @click="switchToLogin">Sign In</strong> to log in with your existing account.
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
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </span>
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
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
              </span>
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
                aria-label="Toggle password visibility"
              >
                <svg v-if="showPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-svg">
                  <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                  <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                  <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                  <line x1="2" y1="2" x2="22" y2="22" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-svg">
                  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>

            <!-- Sub-links: Forgot Password & Login with OTP -->
            <div class="auth-sublinks-row">
              <a href="#" class="text-sublink" @click.prevent="switchToForgot">
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

            <!-- Native Google Button (Fallback if GSI iframe not loaded) -->
            <button
              v-if="!isGoogleGsiRendered"
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
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </span>
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
            <!-- Name Row with Title and Names -->
            <div class="name-grid-row">
              <div class="input-field-wrap title-field-wrap">
                <select v-model="regTitle" class="airbnb-select" aria-label="Title">
                  <option value="Mr.">Mr.</option>
                  <option value="Mrs.">Mrs.</option>
                  <option value="Ms.">Ms.</option>
                </select>
              </div>
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

            <!-- Email Field with Send Button on Right -->
            <div class="input-field-wrap with-action-btn" :class="{ 'is-verified': isEmailVerified }">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </span>
              <input
                v-model="regEmail"
                type="email"
                required
                :readonly="isEmailVerified"
                placeholder="Email address"
                class="airbnb-input has-right-action"
                @input="handleEmailChange"
              />
              <div class="field-right-action">
                <button
                  v-if="!isEmailVerified"
                  type="button"
                  class="btn-action-send"
                  :disabled="!isValidEmail(regEmail) || isRegisterSendingOtp || registerCountdown > 0"
                  @click="handleSendRegisterOtp"
                  title="Send verification OTP"
                >
                  <span v-if="isRegisterSendingOtp">Sending...</span>
                  <span v-else-if="registerCountdown > 0">{{ registerCountdown }}s</span>
                  <span v-else-if="isRegisterOtpSent">Resend</span>
                  <span v-else>Send</span>
                </button>
                <div v-else class="badge-verified-inline">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="verified-svg">
                    <polyline points="20 6 9 17 4 12" />
                  </svg>
                  <span>Verified</span>
                </div>
              </div>
            </div>

            <!-- Underneath OTP Field (Auto-appears when OTP is sent) with Verify Button on Right -->
            <div v-if="isRegisterOtpSent && !isEmailVerified" class="input-field-wrap with-action-btn otp-appear-field">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="18" height="18" x="3" y="3" rx="2" />
                  <path d="M7 11h10M7 15h10M7 7h4" />
                </svg>
              </span>
              <input
                v-model="regOtpCode"
                type="text"
                maxlength="6"
                placeholder="Enter 6-digit OTP from email"
                class="airbnb-input has-right-action"
                autofocus
              />
              <div class="field-right-action">
                <button
                  type="button"
                  class="btn-action-verify"
                  :disabled="regOtpCode.length < 6 || isRegisterVerifyingOtp"
                  @click="handleVerifyRegisterOtp"
                >
                  <span v-if="isRegisterVerifyingOtp">Checking...</span>
                  <span v-else>Verify</span>
                </button>
              </div>
            </div>

            <!-- Status / Feedback message -->
            <div v-if="registerSuccessMsg" class="reg-feedback-note" :class="{ 'is-success': isEmailVerified }">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="note-svg">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="16" x2="12" y2="12" />
                <line x1="12" y1="8" x2="12.01" y2="8" />
              </svg>
              <span>{{ registerSuccessMsg }}</span>
            </div>

            <!-- Phone -->
            <div class="input-field-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="14" height="20" x="5" y="2" rx="2" ry="2" />
                  <path d="M12 18h.01" />
                </svg>
              </span>
              <input
                v-model="regPhone"
                type="tel"
                required
                placeholder="WhatsApp Phone Number"
                class="airbnb-input"
              />
            </div>

            <!-- Password with Show/Hide -->
            <div class="input-field-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
              </span>
              <input
                v-model="regPassword"
                :type="showRegPassword ? 'text' : 'password'"
                required
                placeholder="Create Password (min 6 chars)"
                class="airbnb-input has-right-action"
              />
              <button
                type="button"
                class="btn-toggle-eye"
                @click="showRegPassword = !showRegPassword"
                :title="showRegPassword ? 'Hide password' : 'Show password'"
                aria-label="Toggle password visibility"
              >
                <svg v-if="showRegPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-svg">
                  <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                  <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                  <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                  <line x1="2" y1="2" x2="22" y2="22" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-svg">
                  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>

            <!-- Referral Code (Optional) - reflects with marketing module -->
            <div class="input-field-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z" />
                  <line x1="7" y1="7" x2="7.01" y2="7" />
                </svg>
              </span>
              <input
                v-model="regReferralCode"
                type="text"
                placeholder="Referral Code (Optional)"
                class="airbnb-input uppercase-code"
              />
              <span v-if="isReferralAutoFilled" class="badge-referral-param" title="Auto-filled from campaign URL">
                From Link
              </span>
            </div>

            <button
              type="submit"
              class="btn-primary btn-submit-airbnb"
              :disabled="isLoading || (!isEmailVerified && !registrationToken)"
            >
              <span v-if="isLoading">Creating Account...</span>
              <span v-else-if="!isEmailVerified">Verify Email to Complete</span>
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

        <!-- ============================================================== -->
        <!-- VIEW 4: FORGOT PASSWORD REQUEST                                -->
        <!-- ============================================================== -->
        <div v-else-if="viewMode === 'forgot'" class="auth-view-content">
          <div class="title-section">
            <h3 class="auth-title">Reset Password</h3>
            <p class="auth-subtitle">
              Enter your registered email address and we'll send you instructions to reset your password.
            </p>
          </div>

          <div v-if="forgotSuccessMsg" class="auth-alert-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="alert-svg">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
              <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            <div class="success-msg-wrap">
              <span class="success-text">{{ forgotSuccessMsg }}</span>
            </div>
          </div>

          <form v-if="!forgotSuccessMsg" @submit.prevent="handleForgotPassword" class="form-unified">
            <div class="input-field-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </span>
              <input
                v-model="forgotEmail"
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
                @click="forgotEmail = 'madewedaoffice@gmail.com'"
              >
                madewedaoffice@gmail.com
              </button>
            </div>

            <button
              type="submit"
              class="btn-primary btn-submit-airbnb"
              :disabled="isLoading"
            >
              <span v-if="isLoading">Sending Instructions...</span>
              <span v-else>Send Reset Instructions</span>
            </button>
          </form>

          <div class="auth-sublinks-row mt-3">
            <a href="#" class="text-sublink text-rausch" @click.prevent="switchToReset">
              Already have a reset token? Enter Token &rarr;
            </a>
          </div>

          <div class="auth-bottom-switch">
            <a href="#" class="text-back-link" @click.prevent="switchToLogin">
              &larr; Back to Email & Password Login
            </a>
          </div>
        </div>

        <!-- ============================================================== -->
        <!-- VIEW 5: RESET PASSWORD FORM                                    -->
        <!-- ============================================================== -->
        <div v-else-if="viewMode === 'reset'" class="auth-view-content">
          <div class="title-section">
            <h3 class="auth-title">Set New Password</h3>
            <p class="auth-subtitle">
              Enter the reset token received in your email and your new password.
            </p>
          </div>

          <div v-if="resetSuccessMsg" class="auth-alert-success">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="alert-svg">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
              <polyline points="22 4 12 14.01 9 11.01" />
            </svg>
            <div class="success-msg-wrap">
              <span class="success-text">{{ resetSuccessMsg }}</span>
              <span class="error-hint">Redirecting to login...</span>
            </div>
          </div>

          <form v-if="!resetSuccessMsg" @submit.prevent="handleResetPassword" class="form-unified">
            <!-- Email -->
            <div class="input-field-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="20" height="16" x="2" y="4" rx="2" />
                  <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                </svg>
              </span>
              <input
                v-model="resetEmail"
                type="email"
                required
                placeholder="Member email"
                class="airbnb-input"
              />
            </div>

            <!-- Token -->
            <div class="input-field-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <path d="m21 2-2 2m-6 6 2-2m-4 4 2-2m-6 6 2-2M3 21l3-3m0 0 8-8a4.24 4.24 0 0 0-6-6l-8 8a4.24 4.24 0 0 0 6 6z" />
                </svg>
              </span>
              <input
                v-model="resetToken"
                type="text"
                required
                placeholder="Reset Token (from email)"
                class="airbnb-input"
              />
            </div>

            <!-- New Password -->
            <div class="input-field-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
              </span>
              <input
                v-model="resetNewPassword"
                :type="showResetPassword ? 'text' : 'password'"
                required
                placeholder="New Password (min 6 chars)"
                class="airbnb-input"
              />
              <button
                type="button"
                class="btn-toggle-eye"
                @click="showResetPassword = !showResetPassword"
                :title="showResetPassword ? 'Hide password' : 'Show password'"
              >
                <svg v-if="showResetPassword" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-svg">
                  <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                  <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                  <path d="M6.61 6.61A13.526 13.526 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                  <line x1="2" y1="2" x2="22" y2="22" />
                </svg>
                <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="eye-svg">
                  <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                  <circle cx="12" cy="12" r="3" />
                </svg>
              </button>
            </div>

            <!-- Confirm Password -->
            <div class="input-field-wrap">
              <span class="input-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="field-svg">
                  <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                  <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                </svg>
              </span>
              <input
                v-model="resetConfirmPassword"
                :type="showResetPassword ? 'text' : 'password'"
                required
                placeholder="Confirm New Password"
                class="airbnb-input"
              />
            </div>

            <button
              type="submit"
              class="btn-primary btn-submit-airbnb"
              :disabled="isLoading"
            >
              <span v-if="isLoading">Updating Password...</span>
              <span v-else>Save New Password</span>
            </button>
          </form>

          <div class="auth-bottom-switch">
            <a href="#" class="text-back-link" @click.prevent="switchToLogin">
              &larr; Back to Email & Password Login
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

const GOOGLE_CLIENT_ID = import.meta.env.VITE_GOOGLE_CLIENT_ID || '840783649088-tabdq0ps1i6e1d2vtfsaffsdgs9apcq9.apps.googleusercontent.com'
const isGoogleGsiRendered = ref(false)

const {
  isAuthModalOpen,
  closeAuthModal,
  propertyContext,
  fetchPropertyContext,
  loginWithPassword,
  forgotPassword,
  checkResetToken,
  resetPassword,
  requestOtp,
  verifyOtp,
  loginWithGoogle,
  requestRegisterOtp,
  verifyRegisterOtp,
  registerMember,
  isLoading,
  authError,
} = useAuth()

const { activePropertyId, activeProperty } = useBooking()

const modalCardRef = ref(null)
const viewMode = ref('login') // 'login' | 'otp' | 'register' | 'forgot' | 'reset'

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
const regTitle = ref('Mr.')
const regFirstName = ref('Aditya')
const regLastName = ref('Satriawan')
const regEmail = ref('')
const regPhone = ref('+6281234567890')
const regPassword = ref('Passw0rd123!')
const showRegPassword = ref(false)
const regReferralCode = ref('')
const isReferralAutoFilled = ref(false)

// Register Email OTP Verification States
const regOtpCode = ref('')
const isRegisterOtpSent = ref(false)
const isEmailVerified = ref(false)
const registrationToken = ref('')
const isRegisterSendingOtp = ref(false)
const isRegisterVerifyingOtp = ref(false)
const registerCountdown = ref(0)
let registerTimer = null
const registerSuccessMsg = ref('')

// Sync Referral Code from URL parameters (?referral=... / ?ref=... / ?referral_code=...)
const syncReferralFromUrl = () => {
  if (typeof window !== 'undefined') {
    const params = new URLSearchParams(window.location.search)
    const code = params.get('referral') || params.get('ref') || params.get('referral_code')
    if (code) {
      regReferralCode.value = code.trim()
      isReferralAutoFilled.value = true
      try {
        sessionStorage.setItem('stayhub_referral_code', code.trim())
      } catch (e) {}
    } else {
      try {
        const stored = sessionStorage.getItem('stayhub_referral_code')
        if (stored) {
          regReferralCode.value = stored
          isReferralAutoFilled.value = true
        }
      } catch (e) {}
    }
  }
}

// Lifecycle & Watchers
onMounted(() => {
  if (activePropertyId.value) {
    fetchPropertyContext(activePropertyId.value)
  }
  syncReferralFromUrl()
  initGoogleAuth()
})

watch(activePropertyId, (newId) => {
  if (newId) {
    fetchPropertyContext(newId)
  }
})

watch(isAuthModalOpen, (isOpen) => {
  if (isOpen) {
    authError.value = ''
    viewMode.value = 'login'
    otpRequested.value = false
    syncReferralFromUrl()
    nextTick(() => {
      animateModalOpen()
      initGoogleAuth()
    })
  } else {
    clearInterval(timerInterval)
    clearInterval(registerTimer)
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
  syncReferralFromUrl()
  animateViewSwitch()
}

const switchToForgot = () => {
  viewMode.value = 'forgot'
  authError.value = ''
  forgotSuccessMsg.value = ''
  animateViewSwitch()
}

const switchToReset = () => {
  viewMode.value = 'reset'
  authError.value = ''
  resetSuccessMsg.value = ''
  animateViewSwitch()
}

// Forgot & Reset Password States
const forgotEmail = ref('madewedaoffice@gmail.com')
const forgotSuccessMsg = ref('')

const resetEmail = ref('madewedaoffice@gmail.com')
const resetToken = ref('')
const resetNewPassword = ref('')
const resetConfirmPassword = ref('')
const showResetPassword = ref(false)
const resetSuccessMsg = ref('')

const handleForgotPassword = async () => {
  if (!forgotEmail.value) return
  authError.value = ''
  forgotSuccessMsg.value = ''
  try {
    const res = await forgotPassword(activePropertyId.value, forgotEmail.value)
    forgotSuccessMsg.value = res.data?.message || res.message || 'Password reset instructions have been sent to your email.'
  } catch (err) {
    authError.value = err.message || 'Failed to send reset instructions.'
  }
}

const handleResetPassword = async () => {
  if (!resetEmail.value || !resetToken.value || !resetNewPassword.value) return
  if (resetNewPassword.value !== resetConfirmPassword.value) {
    authError.value = 'Passwords do not match.'
    return
  }
  authError.value = ''
  resetSuccessMsg.value = ''
  try {
    const res = await resetPassword(
      activePropertyId.value,
      resetEmail.value,
      resetToken.value,
      resetNewPassword.value,
      resetConfirmPassword.value
    )
    resetSuccessMsg.value = res.data?.message || res.message || 'Password updated successfully!'
    setTimeout(() => {
      switchToLogin()
    }, 2000)
  } catch (err) {
    authError.value = err.message || 'Failed to reset password.'
  }
}

// Google Identity Services (GIS)
const initGoogleAuth = () => {
  if (typeof window !== 'undefined' && window.google?.accounts?.id) {
    try {
      window.google.accounts.id.initialize({
        client_id: GOOGLE_CLIENT_ID,
        callback: handleGoogleCredentialResponse,
        auto_select: false,
        use_fedcm_for_prompt: true,
      })

      const mountEl = document.getElementById('google-btn-mount')
      if (mountEl) {
        mountEl.innerHTML = ''
        window.google.accounts.id.renderButton(mountEl, {
          theme: 'outline',
          size: 'large',
          width: mountEl.offsetWidth || 340,
          text: 'continue_with',
          shape: 'rectangular',
          logo_alignment: 'left',
        })
        isGoogleGsiRendered.value = true
      }
    } catch (err) {
      console.warn('Google GSI notice:', err.message)
      isGoogleGsiRendered.value = false
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
  authError.value = ''

  // Attempt to trigger the native rendered Google button if mounted
  const renderedBtn = document.querySelector('#google-btn-mount [role="button"], #google-btn-mount iframe')
  if (renderedBtn) {
    renderedBtn.click()
    return
  }

  // If prompt is called, handle One Tap notification events accurately
  if (window.google?.accounts?.id) {
    window.google.accounts.id.prompt((notification) => {
      if (notification.isSkippedMoment()) {
        const reason = notification.getSkippedReason?.() || ''
        console.warn('Google GSI prompt skipped reason:', reason)
        if (reason === 'suppressed_by_user') {
          authError.value = 'Google One Tap sedang dalam masa cooldown browser (karena sebelumnya pernah ditutup). Silakan klik tombol resmi Google di atas atau gunakan Dev 1-Click SSO.'
        } else if (reason === 'origin_or_cookie_blocked') {
          authError.value = 'Browser memblokir third-party cookies atau tracking Google. Silakan periksa pengaturan adblocker/privasi browser atau gunakan Dev 1-Click SSO.'
        } else {
          authError.value = `Google One Tap dilewati (${reason}). Silakan gunakan tombol Dev 1-Click SSO.`
        }
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

// Registration Email & Verification Handlers
const isValidEmail = (email) => {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(String(email || '').trim())
}

const handleEmailChange = () => {
  if (isEmailVerified.value || isRegisterOtpSent.value) {
    isEmailVerified.value = false
    isRegisterOtpSent.value = false
    registrationToken.value = ''
    regOtpCode.value = ''
    registerSuccessMsg.value = ''
    clearInterval(registerTimer)
    registerCountdown.value = 0
  }
}

const handleSendRegisterOtp = async () => {
  if (!isValidEmail(regEmail.value) || isRegisterSendingOtp.value || registerCountdown.value > 0) return
  isRegisterSendingOtp.value = true
  authError.value = ''
  registerSuccessMsg.value = ''
  try {
    const res = await requestRegisterOtp(activePropertyId.value, regEmail.value.trim())
    isRegisterOtpSent.value = true
    registerSuccessMsg.value = res?.data?.message || 'Verification OTP sent to your email. (Check Mailpit at http://localhost:8025)'
    registerCountdown.value = 120
    clearInterval(registerTimer)
    registerTimer = setInterval(() => {
      if (registerCountdown.value > 0) {
        registerCountdown.value--
      } else {
        clearInterval(registerTimer)
      }
    }, 1000)
  } catch (err) {
    authError.value = err.message || 'Failed to dispatch registration OTP code.'
  } finally {
    isRegisterSendingOtp.value = false
  }
}

const handleVerifyRegisterOtp = async () => {
  if (!regOtpCode.value || regOtpCode.value.trim().length < 6 || isRegisterVerifyingOtp.value) return
  isRegisterVerifyingOtp.value = true
  authError.value = ''
  try {
    const res = await verifyRegisterOtp(activePropertyId.value, regEmail.value.trim(), regOtpCode.value.trim())
    registrationToken.value = res.registration_token
    isEmailVerified.value = true
    registerSuccessMsg.value = 'Email successfully verified! You can now join Jeevawasa Club below.'
    clearInterval(registerTimer)
    registerCountdown.value = 0
  } catch (err) {
    authError.value = err.message || 'Invalid or expired OTP code. Please check and try again.'
  } finally {
    isRegisterVerifyingOtp.value = false
  }
}

const handleRegisterSubmit = async () => {
  if (!isEmailVerified.value || !registrationToken.value) {
    authError.value = 'Please click Send to request an OTP and verify your email address first.'
    return
  }

  try {
    await registerMember(activePropertyId.value, {
      title: regTitle.value,
      first_name: regFirstName.value.trim(),
      last_name: regLastName.value.trim(),
      email: regEmail.value.trim(),
      phone: regPhone.value.trim(),
      password: regPassword.value,
      registration_token: registrationToken.value,
      referral_code: regReferralCode.value ? regReferralCode.value.trim() : null,
    })
  } catch (err) {
    authError.value = err.message || 'Registration failed. Please review your details and try again.'
  }
}
</script>

<style scoped>
.modal-scrim {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.55);
  backdrop-filter: blur(3px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 2000;
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

.auth-alert-warning {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: #fffbeb;
  border: 1px solid rgba(245, 158, 11, 0.3);
  border-radius: 8px;
  padding: 10px 12px;
  margin-bottom: 16px;
  font-size: 12px;
  color: #b45309;
}

.auth-alert-warning .alert-svg {
  color: #d97706;
}

.auth-alert-success {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: #f0fdf4;
  border: 1px solid rgba(16, 185, 129, 0.2);
  border-radius: 8px;
  padding: 10px 12px;
  margin-bottom: 16px;
  font-size: 12px;
  color: #15803d;
}

.success-msg-wrap {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.success-text {
  font-weight: 500;
}

.text-back-link {
  font-size: 12px;
  color: var(--colors-body);
  text-decoration: none;
  transition: color 0.15s ease;
}

.text-back-link:hover {
  color: var(--colors-text);
  text-decoration: underline;
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
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--colors-muted);
  pointer-events: none;
}

.field-svg {
  width: 16px;
  height: 16px;
}

.close-dialog-svg {
  width: 14px;
  height: 14px;
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
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--colors-muted);
  background: none;
  border: none;
  cursor: pointer;
  padding: 4px;
}

.btn-toggle-eye:hover {
  color: var(--colors-ink);
}

.eye-svg {
  width: 16px;
  height: 16px;
}

.two-col-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 8px;
}

.two-col-grid .airbnb-input {
  padding-left: 12px;
}

.name-grid-row {
  display: grid;
  grid-template-columns: 86px 1fr 1fr;
  gap: 8px;
}

.name-grid-row .title-field-wrap {
  position: relative;
}

.airbnb-select {
  width: 100%;
  height: 48px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  background-color: #f8fafc;
  padding: 0 10px;
  font-size: 14px;
  font-weight: 500;
  color: #1e293b;
  cursor: pointer;
  outline: none;
  transition: all 0.2s ease;
}

.airbnb-select:focus {
  border-color: #0f172a;
  background-color: #ffffff;
  box-shadow: 0 0 0 1px #0f172a;
}

.name-grid-row .airbnb-input {
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
  display: flex;
  width: 100%;
  justify-content: center;
  min-height: 44px;
}

/* Register Email OTP & Verification Actions */
.with-action-btn {
  position: relative;
}

.with-action-btn .has-right-action {
  padding-right: 90px;
}

.field-right-action {
  position: absolute;
  right: 6px;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  align-items: center;
  z-index: 2;
}

.btn-action-send {
  font-size: 11.5px;
  font-weight: 700;
  color: #ffffff;
  background: var(--colors-primary);
  border: none;
  border-radius: 6px;
  padding: 6px 12px;
  cursor: pointer;
  transition: all 0.15s ease;
  white-space: nowrap;
}

.btn-action-send:hover:not(:disabled) {
  background: var(--colors-primary-active);
}

.btn-action-send:disabled {
  background: var(--colors-hairline-soft);
  color: var(--colors-muted);
  cursor: not-allowed;
  border: 1px solid var(--colors-hairline);
}

.btn-action-verify {
  font-size: 11.5px;
  font-weight: 700;
  color: #ffffff;
  background: #15803d;
  border: none;
  border-radius: 6px;
  padding: 6px 14px;
  cursor: pointer;
  transition: all 0.15s ease;
  white-space: nowrap;
}

.btn-action-verify:hover:not(:disabled) {
  background: #166534;
}

.btn-action-verify:disabled {
  background: var(--colors-hairline-soft);
  color: var(--colors-muted);
  cursor: not-allowed;
  border: 1px solid var(--colors-hairline);
}

.badge-verified-inline {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 11px;
  font-weight: 700;
  color: #15803d;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  padding: 4px 8px;
  border-radius: 6px;
  white-space: nowrap;
}

.badge-verified-inline .verified-svg {
  width: 13px;
  height: 13px;
}

.otp-appear-field {
  animation: otpFieldAppear 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes otpFieldAppear {
  from {
    opacity: 0;
    transform: translateY(-6px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.reg-feedback-note {
  display: flex;
  align-items: flex-start;
  gap: 6px;
  font-size: 11.5px;
  color: #475569;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 8px 10px;
  border-radius: 6px;
  margin-top: -2px;
  margin-bottom: 6px;
  line-height: 1.4;
}

.reg-feedback-note.is-success {
  color: #15803d;
  background: #f0fdf4;
  border-color: #bbf7d0;
}

.reg-feedback-note .note-svg {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
  margin-top: 1px;
}

.uppercase-code {
  text-transform: uppercase;
  font-weight: 600;
  letter-spacing: 0.8px;
}

.badge-referral-param {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  font-size: 9.5px;
  font-weight: 700;
  color: #0284c7;
  background: #f0f9ff;
  border: 1px solid #bae6fd;
  padding: 2px 6px;
  border-radius: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
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
  .name-grid-row {
    grid-template-columns: 78px 1fr 1fr;
    gap: 6px;
  }
  .airbnb-select {
    padding: 0 6px;
    font-size: 13px;
  }
  .name-grid-row .airbnb-input {
    padding-left: 8px;
    font-size: 13px;
  }
}
</style>
