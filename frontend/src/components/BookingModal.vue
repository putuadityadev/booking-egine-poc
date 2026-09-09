<template>
  <div v-if="isBookingModalOpen && selectedRoomForBooking" class="modal-scrim" @click.self="closeBookingModal">
    <div class="booking-dialog-card">
      <!-- Modal Header -->
      <div class="dialog-header">
        <button class="close-btn" @click="closeBookingModal" title="Close" aria-label="Close modal">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="close-svg">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
        <h3 class="dialog-title">Review and Confirm Reservation</h3>
        <div class="header-spacer"></div>
      </div>

      <div class="dialog-body">
        <!-- Room Summary Box -->
        <div class="room-summary-box">
          <img
            :src="selectedRoomForBooking.image_url"
            :alt="selectedRoomForBooking.name"
            class="summary-thumb"
          />
          <div class="summary-details">
            <div class="property-tag">{{ activeProperty?.name }}</div>
            <h4 class="summary-room-name">{{ selectedRoomForBooking.name }}</h4>
            <div class="summary-meta">
              {{ selectedRoomForBooking.bed_type }} • {{ selectedRoomForBooking.size_sqm }} m² • {{ guests }} Guests
            </div>
          </div>
        </div>

        <div class="dialog-content-grid">
          <!-- Left Column: Dates & Guest Form -->
          <div class="form-column">
            <h4 class="section-title">Trip Details</h4>

            <!-- Date Selectors -->
            <div class="dates-selector-grid">
              <div class="date-field">
                <label class="field-label">Check-in</label>
                <input v-model="localCheckIn" type="date" class="date-input" :min="today" />
              </div>
              <div class="date-field">
                <label class="field-label">Check-out</label>
                <input v-model="localCheckOut" type="date" class="date-input" :min="localCheckIn || today" />
              </div>
            </div>

            <div class="guests-counter-row">
              <label class="field-label">Guests</label>
              <select v-model="localGuests" class="guests-select">
                <option :value="1">1 Guest</option>
                <option :value="2">2 Guests</option>
                <option :value="3">3 Guests</option>
                <option :value="4">4 Guests</option>
              </select>
            </div>

            <h4 class="section-title" style="margin-top: 20px;">Guest Information</h4>

            <div class="form-group">
              <label class="field-label">Full Name</label>
              <input v-model="guestName" type="text" class="text-input" placeholder="e.g. Made Weda" />
            </div>

            <div class="form-group">
              <label class="field-label">Email Address</label>
              <input v-model="guestEmail" type="email" class="text-input" placeholder="name@example.com" />
            </div>

            <div class="form-group">
              <label class="field-label">Phone Number (WhatsApp)</label>
              <input v-model="guestPhone" type="tel" class="text-input" placeholder="+6281234567890" />
            </div>
          </div>

          <!-- Right Column: Price Breakdown & Loyalty Reward -->
          <div class="pricing-column">
            <h4 class="section-title">Price Details</h4>

            <div class="price-breakdown-card">
              <!-- Base Calculation -->
              <div class="price-row">
                <span>IDR {{ formatCurrency(nightlyRate) }} x {{ calculatedNights }} nights</span>
                <span>IDR {{ formatCurrency(baseSubtotal) }}</span>
              </div>

              <!-- Member Discount -->
              <div v-if="isLoggedIn && discountAmount > 0" class="price-row discount-row">
                <span class="discount-label">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="perk-inline-svg">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                  <span>{{ memberTier }} Member Discount ({{ tierDiscountPercent }}%)</span>
                </span>
                <span class="discount-value">- IDR {{ formatCurrency(discountAmount) }}</span>
              </div>

              <div class="row-divider"></div>

              <!-- Subtotal Before Tax/Service -->
              <div class="price-row subtotal-row">
                <span>Amount before Tax & Service</span>
                <span>IDR {{ formatCurrency(beforeTaxService) }}</span>
              </div>

              <!-- Tax 11% -->
              <div class="price-row tax-row">
                <span>Government Tax (11% PB1)</span>
                <span>IDR {{ formatCurrency(taxAmount) }}</span>
              </div>

              <!-- Service 10% -->
              <div class="price-row service-row">
                <span>Service Charge (10%)</span>
                <span>IDR {{ formatCurrency(serviceAmount) }}</span>
              </div>

              <div class="row-divider"></div>

              <!-- Grand Total -->
              <div class="price-row total-row">
                <span>Total (IDR)</span>
                <div class="total-price-col">
                  <span v-if="!isLoggedIn" class="strikethrough-total">IDR {{ formatCurrency(grandTotal) }}</span>
                  <span class="grand-total-val" :class="{ 'member-highlight': isLoggedIn }">
                    IDR {{ formatCurrency(isLoggedIn ? grandTotal : grandTotal) }}
                  </span>
                </div>
              </div>

              <!-- Member Exclusive Incentive (When Logged Out) -->
              <div v-if="!isLoggedIn" class="member-exclusive-upsell" @click="openAuthModal('signin')">
                <div class="upsell-badge-row">
                  <span class="upsell-pill">MEMBER PRIVILEGE</span>
                  <span class="upsell-savings-text">Save IDR {{ formatCurrency(potentialSavings) }}</span>
                </div>
                <div class="upsell-content">
                  <div class="upsell-pricing">
                    <span class="upsell-member-rate">IDR {{ formatCurrency(potentialMemberGrandTotal) }}</span>
                    <span class="upsell-unit">Member Total</span>
                  </div>
                  <span class="upsell-action">Sign In to Apply &rarr;</span>
                </div>
              </div>

              <!-- Loyalty Reward Projection Banner -->
              <div class="loyalty-projection-banner" :class="{ 'highlight': isLoggedIn }">
                <div class="star-badge">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="perk-badge-svg">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                  </svg>
                </div>
                <div class="projection-text">
                  <div class="projection-title">
                    <template v-if="isLoggedIn">
                      Earn +{{ projectedPoints }} Club Points
                    </template>
                    <template v-else>
                      Unlock +{{ potentialPoints }} Club Points
                    </template>
                  </div>
                  <div class="projection-desc">
                    <template v-if="isLoggedIn">
                      Points automatically credited to your Jeevawasa Club account upon confirmation.
                    </template>
                    <template v-else>
                      Sign in to unlock exclusive member pricing and earn loyalty points on this stay.
                    </template>
                  </div>
                </div>
              </div>
            </div>

            <!-- Booking Error Alert -->
            <div v-if="bookingError" class="booking-error-box">
              {{ bookingError }}
            </div>

            <!-- Action Buttons: Add to Cart and Confirm & Book -->
            <div class="modal-actions-grid">
              <button
                type="button"
                class="btn-modal-action btn-add-to-cart"
                @click="handleAddToCartFromModal"
              >
                <svg class="action-btn-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
                  <path d="M3 6h18" />
                  <path d="M16 10a4 4 0 0 1-8 0" />
                </svg>
                <span>Add to Cart</span>
              </button>

              <button
                type="button"
                class="btn-modal-action btn-confirm-book"
                :disabled="isSubmittingBooking || !guestName || !guestEmail"
                @click="handleDirectCheckoutFromModal"
              >
                <span>Confirm &amp; Book</span>
                <svg class="action-btn-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M5 12h14" />
                  <path d="m12 5 7 7-7 7" />
                </svg>
              </button>
            </div>
            <p class="cancellation-policy-hint">
              Free cancellation up to 48 hours before check-in. Instant confirmation.
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useAuth } from '../composables/useAuth'
import { useBooking } from '../composables/useBooking'
import { useCart } from '../composables/useCart'

const router = useRouter()
const { addToCart, openCart } = useCart()

const {
  isLoggedIn,
  memberProfile,
  memberName,
  memberEmail,
  memberTier,
  memberId,
  updatePoints,
  calculatePoints,
  openAuthModal,
} = useAuth()

const {
  activeProperty,
  activePropertyId,
  selectedRoomForBooking,
  isBookingModalOpen,
  checkIn,
  checkOut,
  guests,
  isSubmittingBooking,
  bookingError,
  closeBookingModal,
  confirmReservation,
} = useBooking()

const localCheckIn = ref(checkIn.value)
const localCheckOut = ref(checkOut.value)
const localGuests = ref(guests.value)

const guestName = ref('')
const guestEmail = ref('')
const guestPhone = ref('+6281234567890')

const today = computed(() => new Date().toISOString().split('T')[0])

// Initialize guest details when modal opens or user logs in
watch(
  () => isBookingModalOpen.value,
  (open) => {
    if (open) {
      localCheckIn.value = checkIn.value
      localCheckOut.value = checkOut.value
      localGuests.value = guests.value
      guestName.value = memberName.value || 'Mr. Made Weda'
      guestEmail.value = memberEmail.value || 'madewedaoffice@gmail.com'
    }
  }
)

const calculatedNights = computed(() => {
  if (!localCheckIn.value || !localCheckOut.value) return 1
  const d1 = new Date(localCheckIn.value)
  const d2 = new Date(localCheckOut.value)
  const diffDays = Math.ceil(Math.abs(d2 - d1) / (1000 * 60 * 60 * 24))
  return diffDays > 0 ? diffDays : 1
})

const nightlyRate = computed(() => {
  return parseFloat(selectedRoomForBooking.value?.base_price || 0)
})

const baseSubtotal = computed(() => {
  return nightlyRate.value * calculatedNights.value
})

const tierDiscountPercent = computed(() => {
  if (!isLoggedIn.value) return 0
  const tier = memberTier.value || 'Diamond'
  const rates = selectedRoomForBooking.value?.tier_discount_rates || { Bronze: 5, Silver: 10, Gold: 15, Diamond: 20 }
  return rates[tier] || 20
})

const discountAmount = computed(() => {
  if (!isLoggedIn.value) return 0
  return Math.round((baseSubtotal.value * tierDiscountPercent.value) / 100)
})

const beforeTaxService = computed(() => {
  return Math.max(0, baseSubtotal.value - discountAmount.value)
})

const taxAmount = computed(() => {
  return Math.round((beforeTaxService.value * 11) / 100)
})

const serviceAmount = computed(() => {
  return Math.round((beforeTaxService.value * 10) / 100)
})

const grandTotal = computed(() => {
  return beforeTaxService.value + taxAmount.value + serviceAmount.value
})

const potentialMemberRate = computed(() => {
  return baseSubtotal.value * 0.8
})

const potentialSavings = computed(() => {
  return baseSubtotal.value * 0.2
})

const potentialMemberGrandTotal = computed(() => {
  const net = potentialMemberRate.value
  const tax = Math.round((net * 11) / 100)
  const service = Math.round((net * 10) / 100)
  return net + tax + service
})

const projectedPoints = computed(() => {
  return calculatePoints(beforeTaxService.value)
})

const potentialPoints = computed(() => {
  return calculatePoints(potentialMemberRate.value)
})

const formatCurrency = (val) => {
  if (isNaN(val)) return '0'
  return new Intl.NumberFormat('id-ID').format(Math.round(val))
}

const handleAddToCartFromModal = () => {
  if (!selectedRoomForBooking.value) return

  addToCart(selectedRoomForBooking.value, activeProperty.value, {
    checkIn: localCheckIn.value,
    checkOut: localCheckOut.value,
    nights: calculatedNights.value,
    guests: localGuests.value,
    guestName: guestName.value.trim(),
    guestEmail: guestEmail.value.trim(),
    guestPhone: guestPhone.value.trim(),
    isMember: isLoggedIn.value,
    memberTier: memberTier.value,
  })

  closeBookingModal()
  openCart()
}

const handleDirectCheckoutFromModal = () => {
  if (!selectedRoomForBooking.value) return

  addToCart(selectedRoomForBooking.value, activeProperty.value, {
    checkIn: localCheckIn.value,
    checkOut: localCheckOut.value,
    nights: calculatedNights.value,
    guests: localGuests.value,
    guestName: guestName.value.trim(),
    guestEmail: guestEmail.value.trim(),
    guestPhone: guestPhone.value.trim(),
    isMember: isLoggedIn.value,
    memberTier: memberTier.value,
  })

  closeBookingModal()
  router.push('/checkout')
}

const handleConfirmReservation = async () => {
  const payload = {
    property_id: activePropertyId.value,
    room_id: selectedRoomForBooking.value.id,
    check_in: localCheckIn.value,
    check_out: localCheckOut.value,
    guests: localGuests.value,
    guest_name: guestName.value.trim(),
    guest_email: guestEmail.value.trim(),
    guest_phone: guestPhone.value.trim(),
    is_member: isLoggedIn.value,
    member_id: memberId.value,
    member_tier: memberTier.value,
  }

  try {
    const res = await confirmReservation(payload)
    if (res?.membership?.points_earned && memberProfile.value?.member) {
      const currentPts = memberProfile.value.member.points || 0
      updatePoints(currentPts + res.membership.points_earned)
    }
  } catch (err) {
    // Error is set in bookingError
  }
}
</script>

<style scoped>
.modal-scrim {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 200;
  padding: 16px;
}

.booking-dialog-card {
  width: 100%;
  max-width: 860px;
  max-height: 90vh;
  background: var(--colors-canvas);
  border-radius: var(--radius-lg);
  border: 1px solid var(--colors-hairline);
  box-shadow: none;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  animation: modalPop 0.25s ease-out;
}

@keyframes modalPop {
  from {
    opacity: 0;
    transform: scale(0.95);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.dialog-header {
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  border-bottom: 1px solid var(--colors-hairline-soft);
  flex-shrink: 0;
}

.close-btn {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: var(--colors-ink);
  transition: background-color 0.15s ease;
}

.close-btn:hover {
  background-color: var(--colors-surface-soft);
}

.dialog-title {
  font-size: 16px;
  font-weight: 700;
  color: var(--colors-ink);
}

.header-spacer {
  width: 32px;
}

.dialog-body {
  padding: 24px;
  overflow-y: auto;
}

/* Room Summary */
.room-summary-box {
  display: flex;
  align-items: center;
  gap: 16px;
  padding: 16px;
  background: var(--colors-surface-soft);
  border-radius: var(--radius-md);
  margin-bottom: 24px;
}

.summary-thumb {
  width: 80px;
  height: 80px;
  border-radius: var(--radius-sm);
  object-fit: cover;
}

.summary-details {
  flex: 1;
}

.property-tag {
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--colors-primary);
  margin-bottom: 2px;
}

.summary-room-name {
  font-size: 18px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 4px;
}

.summary-meta {
  font-size: 13px;
  color: var(--colors-muted);
}

/* 2-Column Dialog Content */
.dialog-content-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 28px;
}

.section-title {
  font-size: 15px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 12px;
}

.field-label {
  display: block;
  font-size: 11px;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  color: var(--colors-ink);
  margin-bottom: 6px;
}

.dates-selector-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 12px;
}

.date-field {
  display: flex;
  flex-direction: column;
}

.date-input,
.guests-select,
.text-input {
  height: 46px;
  border: 1px solid var(--colors-hairline);
  border-radius: var(--radius-sm);
  padding: 0 12px;
  font-size: 14px;
  color: var(--colors-ink);
  outline: none;
  background: var(--colors-canvas);
  transition: border-color 0.2s ease;
  width: 100%;
}

.date-input:focus,
.guests-select:focus,
.text-input:focus {
  border-color: var(--colors-ink);
  border-width: 2px;
}

.form-group {
  margin-bottom: 12px;
}

/* Pricing Card */
.price-breakdown-card {
  background: var(--colors-canvas);
  border: 1px solid var(--colors-hairline-soft);
  border-radius: var(--radius-md);
  padding: 16px;
  box-shadow: none;
  margin-bottom: 16px;
}

.price-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 13px;
  color: var(--colors-body);
  margin-bottom: 8px;
}

.discount-row {
  color: #6d28d9;
  font-weight: 600;
}

.discount-value {
  color: #6d28d9;
}

.subtotal-row {
  font-weight: 600;
  color: var(--colors-ink);
}

.tax-row,
.service-row {
  font-size: 12px;
  color: var(--colors-muted);
}

.row-divider {
  height: 1px;
  background: var(--colors-hairline-soft);
  margin: 10px 0;
}

.total-row {
  font-size: 15px;
  font-weight: 700;
  color: var(--colors-ink);
  margin-bottom: 12px;
  align-items: flex-end;
}

.total-price-col {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.strikethrough-total {
  font-size: 13px;
  color: var(--colors-muted-soft);
  text-decoration: line-through;
  font-weight: 500;
}

.grand-total-val {
  font-size: 20px;
  color: var(--colors-ink);
  font-weight: 800;
}

.grand-total-val.member-highlight {
  color: #ff385c;
}

/* Member Exclusive Upsell */
.member-exclusive-upsell {
  background: #fff1f2;
  border: 1px solid rgba(255, 56, 92, 0.25);
  border-radius: var(--radius-sm);
  padding: 12px 14px;
  margin-bottom: 12px;
  cursor: pointer;
  transition: background-color 0.15s ease, border-color 0.15s ease;
}

.member-exclusive-upsell:hover {
  background: #ffe4e6;
  border-color: #ff385c;
}

.upsell-badge-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 6px;
}

.upsell-pill {
  font-size: 9px;
  font-weight: 800;
  letter-spacing: 0.6px;
  color: #e11d48;
  background: rgba(225, 29, 72, 0.12);
  padding: 2px 6px;
  border-radius: 4px;
}

.upsell-savings-text {
  font-size: 11px;
  font-weight: 700;
  color: #e11d48;
}

.upsell-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.upsell-pricing {
  display: flex;
  align-items: baseline;
  gap: 6px;
}

.upsell-member-rate {
  font-size: 16px;
  font-weight: 800;
  color: #ff385c;
}

.upsell-unit {
  font-size: 11px;
  color: var(--colors-muted);
}

.upsell-action {
  font-size: 11px;
  font-weight: 700;
  color: #e11d48;
  text-decoration: underline;
}

/* Loyalty Projection */
.loyalty-projection-banner {
  display: flex;
  align-items: flex-start;
  gap: 10px;
  background: #f8fafc;
  border: 1px solid #e2e8f0;
  padding: 12px;
  border-radius: var(--radius-sm);
  margin-top: 12px;
}

.loyalty-projection-banner.highlight {
  background: #fdf4ff;
  border-color: #f0abfc;
}

.star-badge {
  font-size: 18px;
}

.projection-title {
  font-size: 13px;
  font-weight: 700;
  color: #701a75;
}

.projection-desc {
  font-size: 11px;
  color: var(--colors-muted);
  line-height: 1.35;
  margin-top: 2px;
}

.modal-actions-grid {
  display: grid;
  grid-template-columns: 1fr 1.2fr;
  gap: 12px;
  margin-top: 14px;
}

.btn-modal-action {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  height: 48px;
  padding: 0 16px;
  border-radius: 10px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.btn-add-to-cart {
  background: var(--colors-canvas);
  color: var(--colors-ink);
  border: 1.5px solid var(--colors-ink);
}

.btn-add-to-cart:hover {
  background: var(--colors-subtle, #f5f5f5);
}

.btn-confirm-book {
  background: var(--colors-ink);
  color: #fff;
  border: 1.5px solid var(--colors-ink);
}

.btn-confirm-book:hover:not(:disabled) {
  opacity: 0.9;
}

.btn-confirm-book:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.action-btn-svg {
  width: 16px;
  height: 16px;
}

.perk-inline-svg {
  width: 13px;
  height: 13px;
  color: #701a75;
  vertical-align: middle;
  margin-right: 4px;
}

.perk-badge-svg {
  width: 18px;
  height: 18px;
  color: #701a75;
}

.close-svg {
  width: 16px;
  height: 16px;
}

.cancellation-policy-hint {
  font-size: 11px;
  color: var(--colors-muted);
  text-align: center;
  margin-top: 8px;
}

.booking-error-box {
  background: #fef2f2;
  border: 1px solid #fecaca;
  color: var(--colors-error);
  font-size: 12px;
  padding: 8px 12px;
  border-radius: var(--radius-sm);
  margin-bottom: 12px;
}

@media (max-width: 744px) {
  .dialog-content-grid {
    grid-template-columns: 1fr;
  }

  .modal-actions-grid {
    grid-template-columns: 1fr;
  }
}
</style>
