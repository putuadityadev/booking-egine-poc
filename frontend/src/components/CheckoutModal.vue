<template>
  <div v-if="isCheckoutModalOpen" class="modal-scrim" @click.self="closeCheckout">
    <div class="checkout-dialog-card">
      <!-- Modal Header -->
      <div class="dialog-header">
        <div class="header-left">
          <button type="button" class="close-btn" @click="closeCheckout" aria-label="Close checkout">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
          <div class="header-titles">
            <h3 class="dialog-title">Review &amp; Checkout</h3>
            <span class="dialog-subtitle">Finalize your stay with instant confirmation</span>
          </div>
        </div>
        <div class="secure-badge">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
          </svg>
          <span>SSL Secured</span>
        </div>
      </div>

      <div class="dialog-body">
        <div class="checkout-grid">
          <!-- Left Column: Guest Information & Payment Gateway Simulation -->
          <div class="main-column">
            <!-- Step 1: Guest Information -->
            <div class="section-card">
              <div class="section-header">
                <div class="step-circle">1</div>
                <h4 class="section-title">Guest Information</h4>
              </div>

              <!-- Logged In Member Chip -->
              <div v-if="isAuthenticated && member" class="member-identified-banner">
                <div class="member-avatar-chip">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                    <circle cx="12" cy="7" r="4" />
                  </svg>
                </div>
                <div class="member-info-text">
                  <span class="member-name">{{ member.name }}</span>
                  <span class="member-tier-pill">{{ memberTier || 'Club' }} Member • VIP Perks Applied</span>
                </div>
              </div>

              <div class="form-grid">
                <div class="form-group full-width">
                  <label class="field-label">Full Name</label>
                  <input
                    v-model="guestName"
                    type="text"
                    class="text-input"
                    placeholder="Enter primary guest full name"
                    required
                  />
                </div>

                <div class="form-group">
                  <label class="field-label">Email Address</label>
                  <input
                    v-model="guestEmail"
                    type="email"
                    class="text-input"
                    placeholder="name@example.com"
                    required
                  />
                </div>

                <div class="form-group">
                  <label class="field-label">Phone Number</label>
                  <input
                    v-model="guestPhone"
                    type="tel"
                    class="text-input"
                    placeholder="+6281234567890"
                    required
                  />
                </div>
              </div>
            </div>

            <!-- Step 2: Payment Gateway Simulation -->
            <div class="section-card">
              <div class="section-header with-action">
                <div class="header-left-wrap">
                  <div class="step-circle">2</div>
                  <div>
                    <h4 class="section-title">Payment Method (Simulated)</h4>
                    <p class="section-desc">Credit / Debit Card test transaction</p>
                  </div>
                </div>

                <!-- 1-Click Auto Fill Test Card Button -->
                <button
                  type="button"
                  class="autofill-test-card-btn"
                  @click="autoFillTestCard"
                  title="Click to automatically fill dummy credit card details"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="lightning-svg">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2" />
                  </svg>
                  <span>Auto-fill Test Card</span>
                </button>
              </div>

              <div class="card-simulator-container">
                <div class="form-group full-width">
                  <label class="field-label">Cardholder Name</label>
                  <input
                    v-model="cardholderName"
                    type="text"
                    class="text-input"
                    placeholder="Name on card"
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
                    <div class="card-brands">
                      <span class="brand-badge">VISA</span>
                      <span class="brand-badge">MC</span>
                    </div>
                  </div>
                </div>

                <div class="form-row-halves">
                  <div class="form-group">
                    <label class="field-label">Expiry Date</label>
                    <input
                      v-model="cardExpiry"
                      type="text"
                      class="text-input"
                      placeholder="MM/YY"
                      maxlength="5"
                    />
                  </div>

                  <div class="form-group">
                    <label class="field-label">CVV / CVC</label>
                    <input
                      v-model="cardCvv"
                      type="password"
                      class="text-input"
                      placeholder="•••"
                      maxlength="4"
                    />
                  </div>
                </div>

                <div class="simulation-note">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <line x1="12" y1="16" x2="12" y2="12" />
                    <line x1="12" y1="8" x2="12.01" y2="8" />
                  </svg>
                  <span>This is a simulated payment gateway. No live credit card will be charged.</span>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column: Reservation Summary & Total -->
          <div class="summary-column">
            <div class="summary-card">
              <h4 class="summary-card-title">Stay Summary</h4>

              <!-- Selected Items List -->
              <div class="summary-items-list">
                <div v-for="item in cartItems" :key="item.id" class="summary-room-item">
                  <img :src="item.roomImage" :alt="item.roomName" class="summary-room-thumb" />
                  <div class="summary-room-info">
                    <h5 class="summary-room-title">{{ item.roomName }}</h5>
                    <span class="summary-room-dates">{{ formatDateRange(item.checkIn, item.checkOut) }}</span>
                    <span class="summary-room-qty">{{ item.nights }} Nights • {{ item.guests }} Guests</span>
                    <div class="summary-room-price">
                      <span v-if="item.isMemberRate" class="strikethrough-sm">
                        {{ formatCurrency(item.basePrice * item.nights) }}
                      </span>
                      <span class="price-bold">{{ formatCurrency(item.nightlyPrice * item.nights) }}</span>
                    </div>
                  </div>
                </div>
              </div>

              <div class="summary-divider"></div>

              <!-- Price Breakdown -->
              <div class="summary-breakdown">
                <div class="breakdown-line">
                  <span>Room Subtotal</span>
                  <span>{{ formatCurrency(cartBaseSubtotal) }}</span>
                </div>

                <div v-if="cartDiscountTotal > 0" class="breakdown-line is-discount">
                  <span class="discount-label">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="discount-svg">
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                    </svg>
                    Member Discount
                  </span>
                  <span>-{{ formatCurrency(cartDiscountTotal) }}</span>
                </div>

                <div class="breakdown-line">
                  <span>Government Tax (11%)</span>
                  <span>{{ formatCurrency(cartTaxValue) }}</span>
                </div>

                <div class="breakdown-line">
                  <span>Service Charge (10%)</span>
                  <span>{{ formatCurrency(cartServiceValue) }}</span>
                </div>

                <div class="summary-divider"></div>

                <div class="breakdown-line is-total">
                  <span>Total Payable</span>
                  <span class="grand-total-amount">{{ formatCurrency(cartGrandTotal) }}</span>
                </div>
              </div>

              <!-- Loyalty Reward Projection Banner -->
              <div class="loyalty-projection-card">
                <div class="projection-icon">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                </div>
                <div class="projection-info">
                  <div class="projection-heading">+{{ cartEstimatedPoints }} Loyalty Points Earned</div>
                  <div class="projection-sub">Credited directly to your member account</div>
                </div>
              </div>

              <!-- Error Message -->
              <div v-if="errorMessage" class="checkout-error-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <circle cx="12" cy="12" r="10" />
                  <line x1="12" y1="8" x2="12" y2="12" />
                  <line x1="12" y1="16" x2="12.01" y2="16" />
                </svg>
                <span>{{ errorMessage }}</span>
              </div>

              <!-- Confirm & Pay Button -->
              <button
                type="button"
                class="confirm-pay-btn"
                :disabled="isProcessing || cartItems.length === 0"
                @click="processPaymentAndConfirm"
              >
                <div v-if="isProcessing" class="spinner-inline">
                  <svg class="spinner-svg" viewBox="0 0 24 24">
                    <circle class="path" cx="12" cy="12" r="10" fill="none" stroke-width="3" />
                  </svg>
                  <span>Processing Payment...</span>
                </div>
                <div v-else class="btn-inner-content">
                  <span>Confirm &amp; Pay {{ formatCurrency(cartGrandTotal) }}</span>
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M5 12h14" />
                    <path d="m12 5 7 7-7 7" />
                  </svg>
                </div>
              </button>

              <p class="cancellation-policy-note">
                By confirming, you agree to the hotel's reservation policies and terms of service.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, onMounted } from 'vue'
import { useCart } from '../composables/useCart'
import { useAuth } from '../composables/useAuth'
import { useBooking } from '../composables/useBooking'

const {
  cartItems,
  isCheckoutModalOpen,
  cartBaseSubtotal,
  cartDiscountTotal,
  cartTaxValue,
  cartServiceValue,
  cartGrandTotal,
  cartEstimatedPoints,
  closeCheckout,
  clearCart,
} = useCart()

const { isAuthenticated, member, memberTier } = useAuth()
const { activeProperty, confirmReservation } = useBooking()

// Guest Information
const guestName = ref('')
const guestEmail = ref('')
const guestPhone = ref('+6281234567890')

// Payment details
const cardholderName = ref('')
const cardNumber = ref('')
const cardExpiry = ref('')
const cardCvv = ref('')

const isProcessing = ref(false)
const errorMessage = ref('')

// Initialize or prefill from authenticated member
const prefillGuestData = () => {
  if (isAuthenticated?.value && member?.value) {
    guestName.value = member.value.name || `${member.value.first_name || ''} ${member.value.last_name || ''}`.trim() || 'John Doe'
    guestEmail.value = member.value.email || 'guest@example.com'
    if (member.value.phone_number) {
      guestPhone.value = member.value.phone_number
    }
  } else if (!guestName.value) {
    guestName.value = 'John Doe'
    guestEmail.value = 'john.doe@example.com'
  }
}

if (isAuthenticated) {
  watch(isAuthenticated, () => {
    prefillGuestData()
  })
}

onMounted(() => {
  prefillGuestData()
})

// 1-Click Auto Fill Test Card
const autoFillTestCard = () => {
  cardholderName.value = guestName.value || 'John Doe'
  cardNumber.value = '4242 4242 4242 4242'
  cardExpiry.value = '12/28'
  cardCvv.value = '888'
}

const formatCurrency = (val) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(val || 0)
}

const formatDateRange = (inDate, outDate) => {
  if (!inDate || !outDate) return 'Flexible dates'
  const d1 = new Date(inDate)
  const d2 = new Date(outDate)
  const opt = { month: 'short', day: 'numeric' }
  return `${d1.toLocaleDateString('en-US', opt)} – ${d2.toLocaleDateString('en-US', opt)}`
}

// Submit payment & confirm booking
const processPaymentAndConfirm = async () => {
  if (!guestName.value || !guestEmail.value) {
    errorMessage.value = 'Please provide primary guest name and email.'
    return
  }

  if (cartItems.value.length === 0) {
    errorMessage.value = 'Your cart is empty. Please select a room.'
    return
  }

  errorMessage.value = ''
  isProcessing.value = true

  try {
    // Simulate payment gateway latency
    await new Promise((resolve) => setTimeout(resolve, 800))

    // Primary item from cart
    const primaryItem = cartItems.value[0]
    const propertyId = primaryItem.propertyId || activeProperty.value?.id || 1

    const bookingPayload = {
      property_id: propertyId,
      room_id: primaryItem.roomId,
      check_in: primaryItem.checkIn,
      check_out: primaryItem.checkOut,
      guests: primaryItem.guests,
      guest_name: guestName.value,
      guest_email: guestEmail.value,
      guest_phone: guestPhone.value,
      is_member: isAuthenticated.value,
      member_id: member.value?.id || null,
      member_tier: memberTier.value || null,
    }

    await confirmReservation(bookingPayload)

    // Clear cart and close checkout modal
    clearCart()
    closeCheckout()
  } catch (err) {
    errorMessage.value = err.message || 'Payment simulation failed. Please try again.'
  } finally {
    isProcessing.value = false
  }
}
</script>

<style scoped>
.modal-scrim {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 210;
  padding: 16px;
  overflow-y: auto;
}

.checkout-dialog-card {
  width: 100%;
  max-width: 960px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
  background: #ffffff;
  border-radius: 16px;
  border: 1px solid #e0e0e0;
  box-shadow: none;
  overflow: hidden;
  animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalPop {
  from {
    opacity: 0;
    transform: scale(0.97);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

/* Header */
.dialog-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 18px 24px;
  border-bottom: 1px solid #ebebeb;
  background: #fafafa;
}

.header-left {
  display: flex;
  align-items: center;
  gap: 16px;
}

.close-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  border: 1px solid #e0e0e0;
  background: #ffffff;
  color: #555555;
  cursor: pointer;
  box-shadow: none;
  transition: background-color 0.15s ease;
}

.close-btn:hover {
  background-color: #f0f0f0;
  color: #111111;
}

.close-btn svg {
  width: 16px;
  height: 16px;
}

.header-titles {
  display: flex;
  flex-direction: column;
}

.dialog-title {
  margin: 0;
  font-size: 17px;
  font-weight: 700;
  color: #111111;
}

.dialog-subtitle {
  font-size: 12px;
  color: #717171;
}

.secure-badge {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 11px;
  font-weight: 600;
  color: #15803d;
  background: #f0fdf4;
  padding: 4px 10px;
  border-radius: 9999px;
  border: 1px solid #dcfce7;
}

.secure-badge svg {
  width: 13px;
  height: 13px;
}

/* Body */
.dialog-body {
  flex: 1;
  overflow-y: auto;
  padding: 24px;
}

.checkout-grid {
  display: grid;
  grid-template-columns: 1.3fr 1fr;
  gap: 24px;
}

@media (max-width: 768px) {
  .checkout-grid {
    grid-template-columns: 1fr;
  }
}

/* Section Cards */
.main-column {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.section-card {
  background: #ffffff;
  border: 1px solid #ebebeb;
  border-radius: 12px;
  padding: 20px;
}

.section-header {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-bottom: 16px;
}

.section-header.with-action {
  justify-content: space-between;
  align-items: flex-start;
}

.header-left-wrap {
  display: flex;
  align-items: flex-start;
  gap: 10px;
}

.step-circle {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background: #111111;
  color: #ffffff;
  font-size: 12px;
  font-weight: 700;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.section-title {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: #111111;
}

.section-desc {
  margin: 2px 0 0;
  font-size: 12px;
  color: #717171;
}

/* Member identified banner */
.member-identified-banner {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 14px;
  background: #fafaf9;
  border: 1px solid #e7e5e4;
  border-radius: 8px;
  margin-bottom: 16px;
}

.member-avatar-chip {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  background: #e7e5e4;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #44403c;
}

.member-avatar-chip svg {
  width: 16px;
  height: 16px;
}

.member-info-text {
  display: flex;
  flex-direction: column;
}

.member-name {
  font-size: 13px;
  font-weight: 600;
  color: #1c1917;
}

.member-tier-pill {
  font-size: 11px;
  color: #854d0e;
  font-weight: 600;
}

/* Form Styles */
.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.form-group.full-width {
  grid-column: span 2;
}

.field-label {
  display: block;
  font-size: 12px;
  font-weight: 600;
  color: #374151;
  margin-bottom: 6px;
}

.text-input {
  width: 100%;
  padding: 10px 12px;
  border: 1px solid #d1d5db;
  border-radius: 8px;
  font-size: 13px;
  color: #111111;
  background: #ffffff;
  box-sizing: border-box;
}

.text-input:focus {
  outline: none;
  border-color: #111111;
}

/* Auto-fill test card button */
.autofill-test-card-btn {
  display: flex;
  align-items: center;
  gap: 6px;
  padding: 6px 12px;
  background: #f4f4f5;
  border: 1px solid #e4e4e7;
  border-radius: 6px;
  font-size: 11px;
  font-weight: 600;
  color: #18181b;
  cursor: pointer;
  transition: background-color 0.15s ease;
}

.autofill-test-card-btn:hover {
  background: #e4e4e7;
}

.lightning-svg {
  width: 12px;
  height: 12px;
  color: #eab308;
}

/* Card Simulator Container */
.card-simulator-container {
  display: flex;
  flex-direction: column;
  gap: 14px;
  margin-top: 8px;
}

.card-input-wrapper {
  position: relative;
}

.card-input {
  padding-right: 80px;
  letter-spacing: 1px;
}

.card-brands {
  position: absolute;
  right: 10px;
  top: 50%;
  transform: translateY(-50%);
  display: flex;
  gap: 4px;
}

.brand-badge {
  font-size: 9px;
  font-weight: 700;
  padding: 2px 4px;
  background: #f3f4f6;
  color: #4b5563;
  border-radius: 4px;
  border: 1px solid #e5e7eb;
}

.form-row-halves {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.simulation-note {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 11px;
  color: #6b7280;
  background: #f9fafb;
  padding: 8px 12px;
  border-radius: 6px;
  border: 1px solid #f3f4f6;
}

.simulation-note svg {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
}

/* Right Summary Card */
.summary-card {
  background: #fafafa;
  border: 1px solid #ebebeb;
  border-radius: 12px;
  padding: 20px;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.summary-card-title {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: #111111;
}

.summary-items-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.summary-room-item {
  display: flex;
  gap: 12px;
  padding: 10px;
  background: #ffffff;
  border: 1px solid #ebebeb;
  border-radius: 8px;
}

.summary-room-thumb {
  width: 60px;
  height: 60px;
  border-radius: 6px;
  object-fit: cover;
  flex-shrink: 0;
}

.summary-room-info {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 2px;
}

.summary-room-title {
  margin: 0;
  font-size: 13px;
  font-weight: 600;
  color: #111111;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.summary-room-dates,
.summary-room-qty {
  font-size: 11px;
  color: #717171;
}

.summary-room-price {
  display: flex;
  align-items: center;
  gap: 6px;
  margin-top: 4px;
}

.strikethrough-sm {
  font-size: 10px;
  color: #999999;
  text-decoration: line-through;
}

.price-bold {
  font-size: 12px;
  font-weight: 700;
  color: #111111;
}

.summary-divider {
  height: 1px;
  background: #e5e5e5;
  margin: 2px 0;
}

/* Breakdown */
.summary-breakdown {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.breakdown-line {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
  color: #4b5563;
}

.breakdown-line.is-discount {
  color: #15803d;
  font-weight: 600;
}

.discount-label {
  display: flex;
  align-items: center;
  gap: 4px;
}

.discount-svg {
  width: 12px;
  height: 12px;
}

.breakdown-line.is-total {
  font-size: 15px;
  font-weight: 700;
  color: #111111;
  padding-top: 4px;
}

.grand-total-amount {
  font-size: 16px;
  font-weight: 700;
  color: #111111;
}

/* Loyalty projection card */
.loyalty-projection-card {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  background: #ffffff;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
}

.projection-icon {
  width: 28px;
  height: 28px;
  border-radius: 50%;
  background: #fef9c3;
  color: #854d0e;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.projection-icon svg {
  width: 14px;
  height: 14px;
}

.projection-info {
  display: flex;
  flex-direction: column;
}

.projection-heading {
  font-size: 12px;
  font-weight: 600;
  color: #111111;
}

.projection-sub {
  font-size: 11px;
  color: #717171;
}

/* Error box */
.checkout-error-box {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: #fef2f2;
  border: 1px solid #fee2e2;
  border-radius: 6px;
  font-size: 12px;
  color: #dc2626;
}

.checkout-error-box svg {
  width: 14px;
  height: 14px;
  flex-shrink: 0;
}

/* Confirm & Pay button */
.confirm-pay-btn {
  width: 100%;
  padding: 14px 20px;
  background: #111111;
  color: #ffffff;
  border: 1px solid #111111;
  border-radius: 8px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: none;
  transition: background-color 0.15s ease;
}

.confirm-pay-btn:hover:not(:disabled) {
  background: #262626;
}

.confirm-pay-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.btn-inner-content {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.btn-inner-content svg {
  width: 16px;
  height: 16px;
}

.spinner-inline {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.spinner-svg {
  animation: rotate 1.5s linear infinite;
  width: 16px;
  height: 16px;
}

.spinner-svg .path {
  stroke: #ffffff;
  stroke-linecap: round;
  animation: dash 1.5s ease-in-out infinite;
}

@keyframes rotate {
  100% {
    transform: rotate(360deg);
  }
}

@keyframes dash {
  0% {
    stroke-dasharray: 1, 150;
    stroke-dashoffset: 0;
  }
  50% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -35;
  }
  100% {
    stroke-dasharray: 90, 150;
    stroke-dashoffset: -124;
  }
}

.cancellation-policy-note {
  margin: 0;
  font-size: 11px;
  color: #888888;
  text-align: center;
  line-height: 1.4;
}
</style>
