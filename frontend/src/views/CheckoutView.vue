<template>
  <div class="checkout-page-root">
    <!-- Top Luxury Navigation Bar -->
    <header class="checkout-navbar">
      <div class="checkout-nav-container">
        <div class="nav-left">
          <router-link :to="backToSanctuaryRoute" class="back-link" title="Return to Sanctuary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="nav-arrow-svg">
              <path d="m15 18-6-6 6-6" />
            </svg>
            <span>Back to Sanctuary</span>
          </router-link>
          <span class="nav-divider">/</span>
          <span class="nav-breadcrumb-active">Review &amp; Checkout</span>
        </div>

        <div class="nav-center">
          <div class="brand-logo-wrap">
            <img
              :src="propertyBrandingLogo || '/hotel-brand-logo.svg'"
              alt="Sanctuary Logo"
              class="brand-logo-img"
              @error="handleBrandLogoError"
            />
            <span class="brand-title">{{ activeProperty?.name || 'StayHub Luxury Sanctuary' }}</span>
          </div>
        </div>

        <div class="nav-right">
          <div class="security-indicator">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="shield-svg">
              <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
            </svg>
            <span>256-bit SSL Encrypted</span>
          </div>
        </div>
      </div>
    </header>

    <!-- Main Content Area -->
    <main class="checkout-main container">
      <!-- State A: Booking Confirmed Screen -->
      <section v-if="confirmedBooking" class="confirmation-section">
        <div class="confirmation-card">
          <div class="celebration-badge-ring">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" class="check-svg">
              <polyline points="20 6 9 17 4 12" />
            </svg>
          </div>
          <h1 class="confirmation-title">Reservation Confirmed!</h1>
          <p class="confirmation-subtitle">
            Your luxury sanctuary stay has been secured and registered with our central membership platform.
          </p>

          <div class="booking-code-chip">
            <span class="code-label">Booking Reference:</span>
            <span class="code-value">{{ confirmedBooking.reservation_code }}</span>
            <span class="status-pill">CONFIRMED</span>
          </div>

          <!-- Loyalty Points Awarded Card -->
          <div v-if="confirmedBooking.membership?.points_earned" class="points-awarded-banner" :class="{ 'is-pending': !confirmedBooking.membership?.is_points_materialized }">
            <div class="points-icon-wrap">
              <svg v-if="confirmedBooking.membership?.is_points_materialized" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sparkle-svg">
                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
              </svg>
              <svg v-else viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sparkle-svg">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
            </div>
            <div class="points-content">
              <div class="points-heading">
                <span v-if="confirmedBooking.membership?.is_points_materialized">
                  +{{ confirmedBooking.membership.points_earned }} Club Points Awarded!
                </span>
                <span v-else>
                  ⏳ +{{ confirmedBooking.membership.points_earned }} Club Points Pending
                </span>
              </div>
              <div class="points-subtext">
                <span v-if="confirmedBooking.membership?.is_points_materialized">
                  Successfully synced and credited to your loyalty account.
                </span>
                <span v-else>
                  Points reserved. Will be automatically released to your account upon {{ confirmedBooking.membership.point_release_mode === 'checkin' ? 'check-in' : 'check-out' }}.
                </span>
              </div>
            </div>
          </div>

          <!-- Details Summary -->
          <div class="confirmation-details-box">
            <div class="summary-line">
              <span class="label">Property</span>
              <span class="value">{{ confirmedBooking.property_name }}</span>
            </div>

            <!-- Multi-Room Breakdown or Single Room -->
            <div v-if="confirmedBooking.items && confirmedBooking.items.length > 1" class="summary-items-block">
              <div class="summary-items-header">
                <span class="label">Reserved Suites &amp; Villas ({{ confirmedBooking.items.length }} Rooms)</span>
              </div>
              <div
                v-for="(itm, idx) in confirmedBooking.items"
                :key="idx"
                class="summary-sub-item"
              >
                <div class="sub-item-name">{{ itm.room_name }}</div>
                <div class="sub-item-meta">
                  {{ itm.check_in }} &mdash; {{ itm.check_out }} &bull; {{ itm.nights }} Nights &bull; {{ itm.guests }} Guests
                </div>
              </div>
            </div>
            <div v-else class="summary-line">
              <span class="label">Suite &amp; Villa</span>
              <span class="value">{{ confirmedBooking.room_name }}</span>
            </div>

            <div v-if="!confirmedBooking.items || confirmedBooking.items.length <= 1" class="summary-line">
              <span class="label">Stay Duration</span>
              <span class="value">{{ confirmedBooking.check_in }} &mdash; {{ confirmedBooking.check_out }} ({{ confirmedBooking.nights }} Nights)</span>
            </div>
            <div class="summary-line">
              <span class="label">Primary Guest</span>
              <span class="value">{{ confirmedBooking.guest_name }} ({{ confirmedBooking.guests }} Guests)</span>
            </div>
            <div class="summary-line line-total">
              <span class="label">Total Paid (IDR)</span>
              <span class="value amount">IDR {{ formatCurrency(confirmedBooking.pricing?.grand_total) }}</span>
            </div>
          </div>

          <div class="confirmation-actions">
            <router-link :to="backToSanctuaryRoute" class="btn-primary btn-explore">
              Explore More Sanctuary Experiences
            </router-link>
          </div>
        </div>
      </section>

      <!-- State B: Empty Cart Screen -->
      <section v-else-if="cartItems.length === 0" class="empty-cart-section">
        <div class="empty-cart-card">
          <div class="empty-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" class="empty-bag-svg">
              <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
              <path d="M3 6h18" />
              <path d="M16 10a4 4 0 0 1-8 0" />
            </svg>
          </div>
          <h2 class="empty-title">Your Reservation Cart is Empty</h2>
          <p class="empty-desc">
            You currently have no suites or villas selected. Browse our sanctuary rooms to select your stay.
          </p>
          <router-link :to="backToSanctuaryRoute" class="btn-primary btn-browse-rooms">
            Browse Available Suites &amp; Villas
          </router-link>
        </div>
      </section>

      <!-- State C: Standard Active Checkout Layout -->
      <div v-else class="checkout-grid">
        <!-- Left Column: Guest Information & Payment Gateway Simulator -->
        <div class="checkout-main-col">
          <div class="page-headline-wrap">
            <h1 class="page-title">Review &amp; Finalize Reservation</h1>
            <p class="page-subtitle">Verify guest information and complete your secure booking.</p>
          </div>

          <!-- Step 1: Primary Guest Information -->
          <section class="checkout-card">
            <div class="card-header">
              <div class="step-badge">1</div>
              <div class="header-text">
                <h2 class="card-title">Primary Guest Information</h2>
                <p class="card-desc">Reservation details will be dispatched to this email and phone number.</p>
              </div>
            </div>

            <!-- Logged-in Member Badge -->
            <div v-if="isAuthenticated && member" class="member-chip-card">
              <div class="member-avatar">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="member-svg">
                  <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                  <circle cx="12" cy="7" r="4" />
                </svg>
              </div>
              <div class="member-meta">
                <div class="member-name">{{ member.name }}</div>
                <div class="member-status">
                  <span class="tier-pill">{{ memberTier || 'Club' }} Tier</span>
                  <span class="perk-note">VIP Member Benefits &amp; Guaranteed Points Credit</span>
                </div>
              </div>
            </div>

            <div class="form-grid">
              <div class="form-group full-width">
                <label class="field-label">Full Name <span class="required">*</span></label>
                <input
                  v-model="guestName"
                  type="text"
                  class="text-input"
                  placeholder="e.g. Made Weda"
                  required
                />
              </div>

              <div class="form-group">
                <label class="field-label">Email Address <span class="required">*</span></label>
                <input
                  v-model="guestEmail"
                  type="email"
                  class="text-input"
                  placeholder="name@example.com"
                  required
                />
              </div>

              <div class="form-group">
                <label class="field-label">WhatsApp Phone Number <span class="required">*</span></label>
                <input
                  v-model="guestPhone"
                  type="tel"
                  class="text-input"
                  placeholder="+6281234567890"
                  required
                />
              </div>
            </div>
          </section>

          <!-- Step 2: Payment Simulator (Credit / Debit Card) -->
          <section class="checkout-card">
            <div class="card-header with-action">
              <div class="header-left">
                <div class="step-badge">2</div>
                <div class="header-text">
                  <h2 class="card-title">Payment Method (Simulated)</h2>
                  <p class="card-desc">Interactive credit card sandbox with automatic autofill.</p>
                </div>
              </div>

              <!-- 1-Click Auto Fill Test Card Button -->
              <button
                type="button"
                class="btn-autofill"
                @click="handleAutoFillCard"
                title="Populate test card credentials"
              >
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="lightning-svg">
                  <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                </svg>
                <span>⚡ Auto-fill Test Card</span>
              </button>
            </div>

            <!-- Card Simulation Fields -->
            <div class="payment-simulator-box">
              <div class="form-group full-width">
                <label class="field-label">Cardholder Name</label>
                <input
                  v-model="cardholderName"
                  type="text"
                  class="text-input"
                  placeholder="Name as it appears on card"
                />
              </div>

              <div class="form-group full-width">
                <label class="field-label">Card Number</label>
                <div class="card-input-wrapper">
                  <input
                    v-model="cardNumber"
                    type="text"
                    class="text-input card-input"
                    placeholder="4242 •••• •••• 4242"
                    maxlength="19"
                  />
                  <div class="card-badges">
                    <span class="badge-brand">VISA</span>
                    <span class="badge-brand">MC</span>
                  </div>
                </div>
              </div>

              <div class="form-row-duo">
                <div class="form-group">
                  <label class="field-label">Expiration Date</label>
                  <input
                    v-model="cardExpiry"
                    type="text"
                    class="text-input"
                    placeholder="MM/YY"
                    maxlength="5"
                  />
                </div>

                <div class="form-group">
                  <label class="field-label">CVV / Security Code</label>
                  <input
                    v-model="cardCvv"
                    type="password"
                    class="text-input"
                    placeholder="•••"
                    maxlength="4"
                  />
                </div>
              </div>

              <div class="sandbox-disclaimer">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="info-svg">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="16" x2="12" y2="12" />
                  <line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>
                <span>
                  Demo Sandbox Mode: No real financial charge is executed. Booking is registered in database and points are synchronized with your membership account.
                </span>
              </div>
            </div>
          </section>

          <!-- Step 3: Special Requests (Optional) -->
          <section class="checkout-card">
            <div class="card-header">
              <div class="step-badge">3</div>
              <div class="header-text">
                <h2 class="card-title">Sanctuary Special Requests (Optional)</h2>
                <p class="card-desc">Let the concierge know about dietary needs, airport pickup, or arrival timing.</p>
              </div>
            </div>

            <div class="form-group full-width">
              <textarea
                v-model="specialRequests"
                rows="3"
                class="textarea-input"
                placeholder="e.g. Quiet upper-floor villa preferred, late check-in around 6 PM, vegetarian breakfast."
              ></textarea>
            </div>
          </section>
        </div>

        <!-- Right Column: Sticky Order Summary & Direct Confirm CTA -->
        <aside class="checkout-sidebar-col">
          <div class="summary-sticky-card">
            <h2 class="summary-heading">Order Summary</h2>

            <!-- Cart Items List -->
            <div class="items-list">
              <div v-for="item in cartItems" :key="item.id" class="summary-item">
                <img
                  :src="item.roomImage || 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=400&q=80'"
                  :alt="item.roomName"
                  class="item-thumbnail"
                />
                <div class="item-details">
                  <div class="item-title-row">
                    <h3 class="item-name">{{ item.roomName }}</h3>
                    <button
                      type="button"
                      class="btn-remove-item"
                      @click="removeFromCart(item.id)"
                      title="Remove room"
                      aria-label="Remove item"
                    >
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="trash-svg">
                        <polyline points="3 6 5 6 21 6" />
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                      </svg>
                    </button>
                  </div>

                  <div class="item-property-sub">{{ item.propertyName }}</div>
                  <div class="item-dates-meta">
                    {{ formatDateRange(item.checkIn, item.checkOut) }} &bull; {{ item.guests }} Guests
                  </div>

                  <!-- Member Perk Chip -->
                  <div v-if="item.isMemberRate" class="member-discount-tag">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="diamond-svg">
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    <span>{{ item.appliedTier }} Member ({{ item.discountPercent }}% OFF)</span>
                  </div>

                  <!-- Stepper & Price Row -->
                  <div class="item-stepper-row">
                    <div class="nights-stepper">
                      <button
                        type="button"
                        class="stepper-btn"
                        :disabled="item.nights <= 1"
                        @click="updateNights(item.id, item.nights - 1)"
                        aria-label="Decrease nights"
                      >
                        &minus;
                      </button>
                      <span class="stepper-val">{{ item.nights }} {{ item.nights === 1 ? 'Night' : 'Nights' }}</span>
                      <button
                        type="button"
                        class="stepper-btn"
                        @click="updateNights(item.id, item.nights + 1)"
                        aria-label="Increase nights"
                      >
                        +
                      </button>
                    </div>

                    <div class="item-total-price">
                      <span v-if="item.isMemberRate" class="strikethrough-val">
                        IDR {{ formatCurrency(item.basePrice * item.nights) }}
                      </span>
                      <span class="active-val">
                        IDR {{ formatCurrency(item.nightlyPrice * item.nights) }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Financial Breakdown Table -->
            <div class="breakdown-table">
              <div class="breakdown-row">
                <span>Room Accommodation Subtotal</span>
                <span>IDR {{ formatCurrency(cartBaseSubtotal) }}</span>
              </div>

              <div v-if="cartDiscountTotal > 0" class="breakdown-row discount-row">
                <span class="discount-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="inline-diamond-svg">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                  Loyalty Member Savings
                </span>
                <span class="discount-amt">- IDR {{ formatCurrency(cartDiscountTotal) }}</span>
              </div>

              <div class="breakdown-row">
                <span>Government Tax (11% PB1)</span>
                <span>IDR {{ formatCurrency(cartTaxValue) }}</span>
              </div>

              <div class="breakdown-row">
                <span>Sanctuary Service Charge (10%)</span>
                <span>IDR {{ formatCurrency(cartServiceValue) }}</span>
              </div>

              <div class="divider-line"></div>

              <div class="breakdown-row total-row">
                <span class="total-title">Total (IDR)</span>
                <span class="total-amount">IDR {{ formatCurrency(cartGrandTotal) }}</span>
              </div>
            </div>

            <!-- Loyalty Points Incentive Banner -->
            <div class="loyalty-incentive-box" :class="{ 'is-member': isAuthenticated }">
              <div class="incentive-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="sparkle-gold-svg">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
              </div>
              <div class="incentive-text">
                <div class="incentive-title">+{{ cartEstimatedPoints }} Jeevawasa Club Points</div>
                <div class="incentive-desc">
                  {{ isAuthenticated ? 'Automatically credited to your member balance upon confirmation.' : 'Sign in to unlock tier discounts and earn loyalty points on this reservation.' }}
                </div>
              </div>
            </div>

            <!-- Error Notification -->
            <div v-if="submissionError" class="submission-error-box">
              {{ submissionError }}
            </div>

            <!-- Confirm & Pay CTA Button -->
            <button
              type="button"
              class="btn-primary btn-submit-payment"
              :disabled="isSubmitting || !isFormValid"
              @click="handleConfirmAndPay"
            >
              <span v-if="isSubmitting" class="submitting-spinner-wrap">
                <span class="mini-spinner"></span>
                <span>Securing Sanctuary &amp; Crediting Points...</span>
              </span>
              <span v-else>
                Confirm &amp; Pay IDR {{ formatCurrency(cartGrandTotal) }}
              </span>
            </button>

            <!-- Policy Guarantee -->
            <div class="policy-guarantee">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="guarantee-check-svg">
                <polyline points="20 6 9 17 4 12" />
              </svg>
              <span>Free cancellation up to 48 hours prior to check-in. Instant confirmation.</span>
            </div>
          </div>
        </aside>
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { useCart } from '../composables/useCart'
import { useAuth } from '../composables/useAuth'
import { useBooking } from '../composables/useBooking'

const router = useRouter()
const route = useRoute()

const {
  cartItems,
  cartBaseSubtotal,
  cartDiscountTotal,
  cartTaxValue,
  cartServiceValue,
  cartGrandTotal,
  cartEstimatedPoints,
  removeFromCart,
  updateNights,
  clearCart,
} = useCart()

const {
  isAuthenticated,
  member,
  memberTier,
  memberProfile,
  memberName,
  memberEmail,
  memberId,
  updatePoints,
  propertyContext,
} = useAuth()

const {
  activeProperty,
  activePropertyId,
  fetchPropertyDetails,
} = useBooking()

// Guest input state
const guestName = ref('')
const guestEmail = ref('')
const guestPhone = ref('+6281234567890')
const specialRequests = ref('')

// Payment state
const cardholderName = ref('Made Weda')
const cardNumber = ref('4242 •••• •••• 4242')
const cardExpiry = ref('12/28')
const cardCvv = ref('888')

// Submission state
const isSubmitting = ref(false)
const submissionError = ref('')
const confirmedBooking = ref(null)

const propertyBrandingLogo = computed(() => {
  return propertyContext.value?.branding?.logo_url || null
})

const handleBrandLogoError = (e) => {
  e.target.src = '/hotel-brand-logo.svg'
}

const backToSanctuaryRoute = computed(() => {
  const propId = route.params.propertyId || activePropertyId.value || '5'
  return `/book/${propId}`
})

const isFormValid = computed(() => {
  return (
    guestName.value.trim().length > 0 &&
    guestEmail.value.trim().length > 0 &&
    cartItems.value.length > 0
  )
})

onMounted(async () => {
  // Prepopulate guest details from member profile or cart item
  if (isAuthenticated.value && member.value) {
    guestName.value = member.value.name || memberName.value || ''
    guestEmail.value = member.value.email || memberEmail.value || ''
    cardholderName.value = guestName.value || 'Made Weda'
  } else if (cartItems.value.length > 0) {
    const first = cartItems.value[0]
    if (first.guestName) guestName.value = first.guestName
    if (first.guestEmail) guestEmail.value = first.guestEmail
    if (first.guestPhone) guestPhone.value = first.guestPhone
  }

  // Ensure active property details are loaded
  const propId = parseInt(route.params.propertyId || activePropertyId.value || '5', 10)
  if (!activeProperty.value || activeProperty.value.id !== propId) {
    await fetchPropertyDetails(propId)
  }
})

const handleAutoFillCard = () => {
  cardholderName.value = guestName.value || 'Made Weda'
  cardNumber.value = '4242 4242 4242 4242'
  cardExpiry.value = '12/28'
  cardCvv.value = '888'
}

const formatCurrency = (val) => {
  if (isNaN(val)) return '0'
  return new Intl.NumberFormat('id-ID').format(Math.round(val || 0))
}

const formatDateRange = (inDate, outDate) => {
  if (!inDate || !outDate) return 'Flexible dates'
  const d1 = new Date(inDate)
  const d2 = new Date(outDate)
  const opt = { month: 'short', day: 'numeric', year: 'numeric' }
  return `${d1.toLocaleDateString('en-US', opt)} – ${d2.toLocaleDateString('en-US', opt)}`
}

const handleConfirmAndPay = async () => {
  if (!isFormValid.value || isSubmitting.value) return

  isSubmitting.value = true
  submissionError.value = ''

  try {
    const primaryItem = cartItems.value[0]
    const propId = primaryItem.propertyId || activePropertyId.value || 5

    const payload = {
      property_id: propId,
      items: cartItems.value.map((item) => ({
        room_id: item.roomId,
        check_in: item.checkIn,
        check_out: item.checkOut,
        guests: item.guests || 2,
        nights: item.nights || 1,
        quantity: item.quantity || 1,
      })),
      room_id: primaryItem.roomId,
      check_in: primaryItem.checkIn,
      check_out: primaryItem.checkOut,
      guests: primaryItem.guests || 2,
      guest_name: guestName.value.trim(),
      guest_email: guestEmail.value.trim(),
      guest_phone: guestPhone.value.trim(),
      is_member: isAuthenticated.value,
      member_id: memberId.value || member.value?.id || null,
      member_tier: memberTier.value || 'Diamond',
      special_requests: specialRequests.value.trim(),
    }

    const bffUrl = import.meta.env.VITE_BFF_API_URL || 'http://localhost:8002'
    const res = await fetch(`${bffUrl}/api/booking/reserve`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
    })

    const json = await res.json()
    if (!res.ok || !json.success) {
      throw new Error(json.message || 'Failed to process payment and reservation.')
    }

    // Success response
    confirmedBooking.value = json.data

    // Credit loyalty points locally in state only if materialized
    if (json.data?.membership?.points_earned && memberProfile.value?.member && json.data.membership.is_points_materialized) {
      const currentPts = memberProfile.value.member.points || 0
      updatePoints(currentPts + json.data.membership.points_earned)
    }

    // Clear cart once order is completed
    clearCart()
  } catch (err) {
    submissionError.value = err.message || 'An unexpected error occurred during reservation.'
  } finally {
    isSubmitting.value = false
  }
}
</script>

<style scoped>
.checkout-page-root {
  min-height: 100vh;
  background-color: var(--colors-canvas, #fafafa);
  color: var(--colors-ink, #1a1a1a);
  font-family: inherit;
  display: flex;
  flex-direction: column;
}

/* Header Navbar */
.checkout-navbar {
  background: #ffffff;
  border-bottom: 1px solid var(--colors-hairline, #e0e0e0);
  padding: 14px 24px;
  position: sticky;
  top: 0;
  z-index: 100;
}

.checkout-nav-container {
  max-width: 1280px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.nav-left {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--colors-ink);
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.2s ease;
}

.back-link:hover {
  opacity: 0.7;
}

.nav-arrow-svg {
  width: 16px;
  height: 16px;
}

.nav-divider {
  color: #a0a0a0;
}

.nav-breadcrumb-active {
  color: #707070;
  font-weight: 500;
}

.nav-center {
  display: flex;
  align-items: center;
}

.brand-logo-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
}

.brand-logo-img {
  width: 32px;
  height: 32px;
  border-radius: 6px;
  object-fit: cover;
  border: 1px solid var(--colors-hairline, #e0e0e0);
}

.brand-title {
  font-size: 14px;
  font-weight: 700;
  letter-spacing: -0.2px;
}

.nav-right {
  display: flex;
  align-items: center;
}

.security-indicator {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  font-size: 12px;
  font-weight: 500;
  color: #15803d;
  background: #f0fdf4;
  padding: 4px 10px;
  border-radius: 20px;
  border: 1px solid #bbf7d0;
}

.shield-svg {
  width: 14px;
  height: 14px;
}

/* Main Layout */
.checkout-main {
  max-width: 1200px;
  margin: 32px auto;
  padding: 0 20px;
  width: 100%;
  flex: 1;
}

.page-headline-wrap {
  margin-bottom: 24px;
}

.page-title {
  font-size: 26px;
  font-weight: 800;
  letter-spacing: -0.5px;
  margin: 0 0 6px 0;
}

.page-subtitle {
  font-size: 14px;
  color: #666;
  margin: 0;
}

.checkout-grid {
  display: grid;
  grid-template-columns: 1fr 420px;
  gap: 32px;
  align-items: start;
}

/* Card Sections */
.checkout-card {
  background: #ffffff;
  border: 1px solid var(--colors-hairline, #e0e0e0);
  border-radius: 14px;
  padding: 24px;
  margin-bottom: 24px;
}

.card-header {
  display: flex;
  align-items: flex-start;
  gap: 14px;
  margin-bottom: 20px;
}

.card-header.with-action {
  justify-content: space-between;
}

.header-left {
  display: flex;
  align-items: flex-start;
  gap: 14px;
}

.step-badge {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: var(--colors-ink, #1a1a1a);
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 13px;
  font-weight: 700;
  flex-shrink: 0;
}

.card-title {
  font-size: 16px;
  font-weight: 700;
  margin: 0 0 4px 0;
}

.card-desc {
  font-size: 12px;
  color: #666;
  margin: 0;
}

/* Member Chip Card */
.member-chip-card {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #faf5ff;
  border: 1px solid #e9d5ff;
  padding: 12px 16px;
  border-radius: 10px;
  margin-bottom: 20px;
}

.member-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #7e22ce;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.member-svg {
  width: 18px;
  height: 18px;
}

.member-meta {
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.member-name {
  font-size: 14px;
  font-weight: 700;
  color: #4c1d95;
}

.member-status {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 12px;
}

.tier-pill {
  background: #6b21a8;
  color: #ffffff;
  padding: 2px 8px;
  border-radius: 12px;
  font-weight: 700;
  font-size: 10px;
}

.perk-note {
  color: #6b21a8;
}

/* Form Styles */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.form-group.full-width {
  grid-column: 1 / -1;
}

.field-label {
  font-size: 12px;
  font-weight: 600;
  color: var(--colors-ink, #1a1a1a);
}

.field-label .required {
  color: #e11d48;
}

.text-input,
.textarea-input {
  border: 1px solid var(--colors-hairline, #e0e0e0);
  background: #fff;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 14px;
  font-family: inherit;
  outline: none;
  transition: border-color 0.2s ease;
}

.text-input:focus,
.textarea-input:focus {
  border-color: var(--colors-ink, #1a1a1a);
}

.textarea-input {
  resize: vertical;
}

/* Auto fill button */
.btn-autofill {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  color: #0f172a;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-autofill:hover {
  background: #e2e8f0;
}

.lightning-svg {
  width: 14px;
  height: 14px;
  color: #f59e0b;
}

/* Payment Simulator */
.payment-simulator-box {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.card-input-wrapper {
  position: relative;
}

.card-input {
  width: 100%;
  padding-right: 90px;
}

.card-badges {
  position: absolute;
  right: 12px;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  gap: 4px;
}

.badge-brand {
  font-size: 10px;
  font-weight: 800;
  padding: 2px 6px;
  border-radius: 4px;
  background: #f1f5f9;
  border: 1px solid #cbd5e1;
  color: #475569;
}

.form-row-duo {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}

.sandbox-disclaimer {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  background: #eff6ff;
  border: 1px solid #bfdbfe;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 11px;
  color: #1e40af;
  line-height: 1.4;
}

.info-svg {
  width: 16px;
  height: 16px;
  flex-shrink: 0;
  margin-top: 1px;
}

/* Sticky Sidebar Order Summary */
.summary-sticky-card {
  background: #ffffff;
  border: 1px solid var(--colors-hairline, #e0e0e0);
  border-radius: 14px;
  padding: 24px;
  position: sticky;
  top: 84px;
}

.summary-heading {
  font-size: 18px;
  font-weight: 700;
  margin: 0 0 16px 0;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--colors-hairline, #e0e0e0);
}

.items-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
  margin-bottom: 20px;
}

.summary-item {
  display: flex;
  gap: 14px;
  padding-bottom: 16px;
  border-bottom: 1px solid var(--colors-hairline, #e0e0e0);
}

.item-thumbnail {
  width: 80px;
  height: 80px;
  border-radius: 8px;
  object-fit: cover;
  flex-shrink: 0;
  border: 1px solid var(--colors-hairline, #e0e0e0);
}

.item-details {
  flex: 1;
  min-width: 0;
}

.item-title-row {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
}

.item-name {
  font-size: 14px;
  font-weight: 700;
  margin: 0;
  line-height: 1.3;
}

.btn-remove-item {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 2px;
  color: #94a3b8;
  transition: color 0.2s ease;
}

.btn-remove-item:hover {
  color: #ef4444;
}

.trash-svg {
  width: 14px;
  height: 14px;
}

.item-property-sub {
  font-size: 11px;
  color: #64748b;
  margin-top: 2px;
}

.item-dates-meta {
  font-size: 12px;
  color: #475569;
  margin-top: 4px;
}

.member-discount-tag {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  background: #fdf4ff;
  border: 1px solid #f0abfc;
  color: #86198f;
  font-size: 10px;
  font-weight: 700;
  padding: 2px 6px;
  border-radius: 4px;
  margin-top: 6px;
}

.diamond-svg {
  width: 12px;
  height: 12px;
}

.item-stepper-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 10px;
}

.nights-stepper {
  display: inline-flex;
  align-items: center;
  border: 1px solid var(--colors-hairline, #e0e0e0);
  border-radius: 6px;
  background: #ffffff;
}

.stepper-btn {
  background: transparent;
  border: none;
  padding: 3px 8px;
  font-size: 13px;
  font-weight: 700;
  cursor: pointer;
}

.stepper-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.stepper-val {
  font-size: 11px;
  font-weight: 600;
  padding: 0 4px;
}

.item-total-price {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.strikethrough-val {
  font-size: 10px;
  color: #94a3b8;
  text-decoration: line-through;
}

.active-val {
  font-size: 13px;
  font-weight: 700;
  color: var(--colors-ink, #1a1a1a);
}

/* Breakdown Table */
.breakdown-table {
  display: flex;
  flex-direction: column;
  gap: 10px;
  font-size: 13px;
  margin-bottom: 16px;
}

.breakdown-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  color: #475569;
}

.breakdown-row.discount-row {
  color: #16a34a;
  font-weight: 600;
}

.discount-label {
  display: flex;
  align-items: center;
  gap: 4px;
}

.inline-diamond-svg {
  width: 14px;
  height: 14px;
}

.divider-line {
  height: 1px;
  background: var(--colors-hairline, #e0e0e0);
  margin: 6px 0;
}

.breakdown-row.total-row {
  font-size: 16px;
  font-weight: 800;
  color: var(--colors-ink, #1a1a1a);
}

.total-amount {
  font-size: 18px;
  font-weight: 800;
}

/* Loyalty Points Incentive */
.loyalty-incentive-box {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: #fffbeb;
  border: 1px solid #fde68a;
  padding: 12px 14px;
  border-radius: 8px;
  margin-bottom: 16px;
}

.loyalty-incentive-box.is-member {
  background: #fdf4ff;
  border-color: #f0abfc;
}

.incentive-icon {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #f59e0b;
  color: #fff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.loyalty-incentive-box.is-member .incentive-icon {
  background: #9333ea;
}

.sparkle-gold-svg {
  width: 14px;
  height: 14px;
}

.incentive-title {
  font-size: 13px;
  font-weight: 700;
  color: #92400e;
}

.loyalty-incentive-box.is-member .incentive-title {
  color: #701a75;
}

.incentive-desc {
  font-size: 11px;
  color: #78350f;
  line-height: 1.35;
  margin-top: 2px;
}

.loyalty-incentive-box.is-member .incentive-desc {
  color: #86198f;
}

/* CTA */
.btn-primary.btn-submit-payment {
  width: 100%;
  height: 52px;
  background: var(--colors-ink, #1a1a1a);
  color: #ffffff;
  border: none;
  border-radius: 10px;
  font-size: 15px;
  font-weight: 700;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-primary.btn-submit-payment:hover:not(:disabled) {
  opacity: 0.92;
}

.btn-primary.btn-submit-payment:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.submitting-spinner-wrap {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.mini-spinner {
  width: 18px;
  height: 18px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.policy-guarantee {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: #64748b;
  margin-top: 12px;
  text-align: center;
  justify-content: center;
}

.guarantee-check-svg {
  width: 14px;
  height: 14px;
  color: #16a34a;
  flex-shrink: 0;
}

.submission-error-box {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: #b91c1c;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 12px;
  margin-bottom: 12px;
}

/* Empty Cart & Confirmation States */
.empty-cart-section,
.confirmation-section {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px 20px;
}

.empty-cart-card,
.confirmation-card {
  max-width: 600px;
  width: 100%;
  background: #ffffff;
  border: 1px solid var(--colors-hairline, #e0e0e0);
  border-radius: 16px;
  padding: 40px 32px;
  text-align: center;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.empty-icon-wrap,
.celebration-badge-ring {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: #f1f5f9;
  color: #475569;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.celebration-badge-ring {
  background: #dcfce7;
  color: #15803d;
}

.empty-bag-svg,
.check-svg {
  width: 32px;
  height: 32px;
}

.empty-title,
.confirmation-title {
  font-size: 22px;
  font-weight: 800;
  margin: 0 0 8px 0;
}

.empty-desc,
.confirmation-subtitle {
  font-size: 14px;
  color: #64748b;
  margin: 0 0 24px 0;
  max-width: 440px;
  line-height: 1.4;
}

.btn-browse-rooms,
.btn-explore {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--colors-ink, #1a1a1a);
  color: #ffffff;
  text-decoration: none;
  font-weight: 600;
  font-size: 14px;
  padding: 12px 24px;
  border-radius: 10px;
  transition: opacity 0.2s ease;
}

.btn-browse-rooms:hover,
.btn-explore:hover {
  opacity: 0.9;
}

.booking-code-chip {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #f8fafc;
  border: 1px solid #cbd5e1;
  padding: 8px 16px;
  border-radius: 20px;
  margin-bottom: 20px;
  font-size: 13px;
}

.code-label {
  color: #64748b;
}

.code-value {
  font-weight: 800;
  letter-spacing: 0.5px;
}

.status-pill {
  background: #15803d;
  color: #ffffff;
  font-size: 10px;
  font-weight: 800;
  padding: 2px 6px;
  border-radius: 4px;
}

.points-awarded-banner {
  display: flex;
  align-items: center;
  gap: 12px;
  background: #fdf4ff;
  border: 1px solid #f0abfc;
  padding: 12px 18px;
  border-radius: 10px;
  margin-bottom: 24px;
  width: 100%;
  text-align: left;
}

.points-awarded-banner.is-pending {
  background: #fffbeb;
  border-color: #fde68a;
}

.points-awarded-banner.is-pending .points-icon-wrap {
  background: #d97706;
}

.points-awarded-banner.is-pending .points-heading {
  color: #92400e;
}

.points-awarded-banner.is-pending .points-subtext {
  color: #b45309;
}

.points-icon-wrap {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: #7e22ce;
  color: #ffffff;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.points-heading {
  font-size: 14px;
  font-weight: 800;
  color: #701a75;
}

.points-subtext {
  font-size: 11px;
  color: #86198f;
}

.confirmation-details-box {
  width: 100%;
  background: #f8fafc;
  border: 1px solid var(--colors-hairline, #e0e0e0);
  border-radius: 10px;
  padding: 16px 20px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-bottom: 24px;
  text-align: left;
  font-size: 13px;
}

.summary-line {
  display: flex;
  justify-content: space-between;
}

.summary-line .label {
  color: #64748b;
}

.summary-line .value {
  font-weight: 600;
  color: var(--colors-ink, #1a1a1a);
}

.summary-line.line-total {
  border-top: 1px solid #e2e8f0;
  padding-top: 10px;
  font-size: 15px;
  font-weight: 800;
}

.summary-line.line-total .amount {
  color: #0f172a;
  font-size: 17px;
}

.summary-items-block {
  display: flex;
  flex-direction: column;
  gap: 8px;
  background: #ffffff;
  padding: 10px 14px;
  border-radius: 8px;
  border: 1px solid #e2e8f0;
}

.summary-items-header .label {
  font-size: 12px;
  font-weight: 700;
  color: #334155;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.summary-sub-item {
  padding-left: 6px;
  border-left: 2px solid #0f172a;
}

.sub-item-name {
  font-weight: 700;
  color: #0f172a;
  font-size: 13px;
}

.sub-item-meta {
  font-size: 12px;
  color: #64748b;
  margin-top: 2px;
}

@media (max-width: 900px) {
  .checkout-grid {
    grid-template-columns: 1fr;
  }

  .summary-sticky-card {
    position: static;
  }

  .form-grid {
    grid-template-columns: 1fr;
  }
}
</style>
