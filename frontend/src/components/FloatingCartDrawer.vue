<template>
  <div class="floating-cart-wrapper" :class="{ 'is-open': isCartOpen }">
    <!-- Floating Collapsed Pill Trigger -->
    <button
      v-if="!isCartOpen && cartCount > 0"
      type="button"
      class="floating-cart-pill"
      @click="openCart"
      aria-label="View booking cart"
    >
      <div class="pill-icon-container">
        <!-- SVG Shopping Bag Icon -->
        <svg class="pill-svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
          <path d="M3 6h18" />
          <path d="M16 10a4 4 0 0 1-8 0" />
        </svg>
        <span class="pill-badge">{{ cartCount }}</span>
      </div>
      <div class="pill-text-container">
        <span class="pill-label">Selected Stay</span>
        <span class="pill-price">{{ formatCurrency(cartGrandTotal) }}</span>
      </div>
      <!-- SVG Chevron Up -->
      <svg class="pill-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="m18 15-6-6-6 6" />
      </svg>
    </button>

    <!-- Expanded Cart Drawer Panel -->
    <div v-if="isCartOpen" class="cart-drawer-panel">
      <!-- Drawer Header -->
      <div class="drawer-header">
        <div class="header-title-wrap">
          <svg class="header-cart-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
            <path d="M3 6h18" />
            <path d="M16 10a4 4 0 0 1-8 0" />
          </svg>
          <div>
            <h3 class="drawer-title">Reservation Cart</h3>
            <p class="drawer-subtitle">{{ cartCount }} {{ cartCount === 1 ? 'room' : 'rooms' }} selected</p>
          </div>
        </div>
        <button type="button" class="drawer-close-btn" @click="closeCart" aria-label="Close cart drawer">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>

      <!-- Drawer Content -->
      <div class="drawer-body">
        <div v-if="cartItems.length === 0" class="empty-cart-state">
          <svg class="empty-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
            <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z" />
            <path d="M3 6h18" />
            <path d="M16 10a4 4 0 0 1-8 0" />
          </svg>
          <p class="empty-title">Your cart is empty</p>
          <p class="empty-desc">Explore our rooms and suites, then add your preferred accommodation.</p>
        </div>

        <div v-else class="cart-items-list">
          <div v-for="item in cartItems" :key="item.id" class="cart-item-card">
            <img :src="item.roomImage" :alt="item.roomName" class="item-thumbnail" />
            <div class="item-content">
              <div class="item-top">
                <h4 class="item-name">{{ item.roomName }}</h4>
                <button
                  type="button"
                  class="item-remove-btn"
                  @click="removeFromCart(item.id)"
                  title="Remove from cart"
                  aria-label="Remove item"
                >
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="3 6 5 6 21 6" />
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />
                  </svg>
                </button>
              </div>

              <div class="item-dates">
                <span>{{ formatDateRange(item.checkIn, item.checkOut) }}</span>
                <span class="dot-separator">•</span>
                <span>{{ item.guests }} {{ item.guests === 1 ? 'Guest' : 'Guests' }}</span>
              </div>

              <!-- Member Discount Badge -->
              <div v-if="item.isMemberRate" class="member-perk-chip">
                <svg class="chip-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                </svg>
                <span>{{ item.appliedTier }} Member ({{ item.discountPercent }}% OFF)</span>
              </div>

              <div class="item-stepper-price">
                <!-- Nights Control -->
                <div class="nights-stepper">
                  <button
                    type="button"
                    class="stepper-btn"
                    :disabled="item.nights <= 1"
                    @click="updateNights(item.id, item.nights - 1)"
                    aria-label="Decrease nights"
                  >
                    -
                  </button>
                  <span class="stepper-value">{{ item.nights }} {{ item.nights === 1 ? 'nt' : 'nts' }}</span>
                  <button
                    type="button"
                    class="stepper-btn"
                    @click="updateNights(item.id, item.nights + 1)"
                    aria-label="Increase nights"
                  >
                    +
                  </button>
                </div>

                <!-- Price display -->
                <div class="item-price-wrap">
                  <span v-if="item.isMemberRate" class="strikethrough-price">
                    {{ formatCurrency(item.basePrice * item.nights) }}
                  </span>
                  <span class="actual-price">
                    {{ formatCurrency(item.nightlyPrice * item.nights) }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Drawer Footer Summary -->
      <div v-if="cartItems.length > 0" class="drawer-footer">
        <!-- Loyalty Perks Preview -->
        <div v-if="isAuthenticated" class="loyalty-preview-banner">
          <div class="preview-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
            </svg>
          </div>
          <div class="preview-text">
            <span class="preview-title">+{{ cartEstimatedPoints }} Points will be earned</span>
            <span class="preview-subtitle">Auto-credited upon confirmed booking</span>
          </div>
        </div>

        <div v-else class="loyalty-preview-banner is-guest">
          <div class="preview-icon-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
              <circle cx="12" cy="7" r="4" />
            </svg>
          </div>
          <div class="preview-text">
            <span class="preview-title">Earn points on this reservation</span>
            <span class="preview-subtitle">Sign in during checkout to claim discounts</span>
          </div>
        </div>

        <!-- Breakdown -->
        <div class="price-breakdown">
          <div class="breakdown-row">
            <span>Room Subtotal</span>
            <span>{{ formatCurrency(cartBaseSubtotal) }}</span>
          </div>
          <div v-if="cartDiscountTotal > 0" class="breakdown-row is-discount">
            <span>Loyalty Member Savings</span>
            <span>-{{ formatCurrency(cartDiscountTotal) }}</span>
          </div>
          <div class="breakdown-row">
            <span>Taxes & Service (21%)</span>
            <span>{{ formatCurrency(cartTaxValue + cartServiceValue) }}</span>
          </div>
          <div class="breakdown-divider"></div>
          <div class="breakdown-row is-total">
            <span>Estimated Total</span>
            <span class="total-amount">{{ formatCurrency(cartGrandTotal) }}</span>
          </div>
        </div>

        <!-- Action CTAs -->
        <div class="drawer-actions">
          <button type="button" class="checkout-btn" @click="handleProceedToCheckout">
            <span>Review &amp; Checkout</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M5 12h14" />
              <path d="m12 5 7 7-7 7" />
            </svg>
          </button>
          <div class="footer-aux-links">
            <button type="button" class="clear-cart-btn" @click="clearCart">Clear Cart</button>
            <button type="button" class="continue-btn" @click="closeCart">Keep Browsing</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router'
import { useCart } from '../composables/useCart'
import { useAuth } from '../composables/useAuth'

const emit = defineEmits(['checkout'])
const router = useRouter()

const {
  cartItems,
  isCartOpen,
  cartCount,
  cartBaseSubtotal,
  cartDiscountTotal,
  cartTaxValue,
  cartServiceValue,
  cartGrandTotal,
  cartEstimatedPoints,
  openCart,
  closeCart,
  removeFromCart,
  updateNights,
  clearCart,
} = useCart()

const { isAuthenticated, member } = useAuth()

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

const handleProceedToCheckout = () => {
  closeCart()
  router.push('/checkout')
  emit('checkout')
}
</script>

<style scoped>
.floating-cart-wrapper {
  position: fixed;
  bottom: 24px;
  right: 24px;
  z-index: 999;
  font-family: inherit;
}

/* Collapsed Pill Button */
.floating-cart-pill {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 18px 10px 14px;
  background-color: #111111;
  color: #ffffff;
  border: 1px solid #222222;
  border-radius: 9999px;
  cursor: pointer;
  box-shadow: none;
  transition: background-color 0.15s ease, transform 0.15s ease;
}

.floating-cart-pill:hover {
  background-color: #222222;
}

.pill-icon-container {
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  background-color: rgba(255, 255, 255, 0.12);
  border-radius: 50%;
}

.pill-svg {
  width: 16px;
  height: 16px;
}

.pill-badge {
  position: absolute;
  top: -4px;
  right: -4px;
  background-color: #ffffff;
  color: #111111;
  font-size: 10px;
  font-weight: 700;
  line-height: 1;
  padding: 2px 5px;
  border-radius: 9999px;
}

.pill-text-container {
  display: flex;
  flex-direction: column;
  text-align: left;
}

.pill-label {
  font-size: 11px;
  color: #999999;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.pill-price {
  font-size: 13px;
  font-weight: 600;
  color: #ffffff;
}

.pill-chevron {
  width: 16px;
  height: 16px;
  color: #888888;
  margin-left: 2px;
}

/* Expanded Cart Drawer Panel */
.cart-drawer-panel {
  width: 380px;
  max-width: calc(100vw - 32px);
  max-height: 82vh;
  display: flex;
  flex-direction: column;
  background-color: #ffffff;
  border: 1px solid #e0e0e0;
  border-radius: 16px;
  box-shadow: none;
  overflow: hidden;
  animation: slideUpFade 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes slideUpFade {
  from {
    opacity: 0;
    transform: translateY(12px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Header */
.drawer-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px 20px;
  border-bottom: 1px solid #ebebeb;
  background-color: #fafafa;
}

.header-title-wrap {
  display: flex;
  align-items: center;
  gap: 10px;
}

.header-cart-icon {
  width: 20px;
  height: 20px;
  color: #111111;
}

.drawer-title {
  margin: 0;
  font-size: 15px;
  font-weight: 600;
  color: #111111;
}

.drawer-subtitle {
  margin: 0;
  font-size: 12px;
  color: #717171;
}

.drawer-close-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 28px;
  height: 28px;
  border-radius: 50%;
  border: 1px solid #e0e0e0;
  background-color: #ffffff;
  color: #555555;
  cursor: pointer;
  box-shadow: none;
  transition: background-color 0.15s ease;
}

.drawer-close-btn:hover {
  background-color: #f0f0f0;
  color: #111111;
}

.drawer-close-btn svg {
  width: 14px;
  height: 14px;
}

/* Body */
.drawer-body {
  flex: 1;
  overflow-y: auto;
  padding: 16px 20px;
}

.empty-cart-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 40px 20px;
  text-align: center;
}

.empty-icon {
  width: 40px;
  height: 40px;
  color: #bbbbbb;
  margin-bottom: 12px;
}

.empty-title {
  margin: 0 0 6px;
  font-size: 14px;
  font-weight: 600;
  color: #222222;
}

.empty-desc {
  margin: 0;
  font-size: 12px;
  color: #717171;
  line-height: 1.4;
}

/* Cart Items List */
.cart-items-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}

.cart-item-card {
  display: flex;
  gap: 12px;
  padding: 12px;
  border: 1px solid #ebebeb;
  border-radius: 10px;
  background-color: #ffffff;
}

.item-thumbnail {
  width: 64px;
  height: 64px;
  border-radius: 8px;
  object-fit: cover;
  flex-shrink: 0;
  border: 1px solid #f0f0f0;
}

.item-content {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.item-top {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  gap: 8px;
}

.item-name {
  margin: 0;
  font-size: 13px;
  font-weight: 600;
  color: #111111;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.item-remove-btn {
  background: none;
  border: none;
  padding: 2px;
  color: #999999;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.item-remove-btn:hover {
  color: #dc2626;
}

.item-remove-btn svg {
  width: 14px;
  height: 14px;
}

.item-dates {
  font-size: 11px;
  color: #717171;
  display: flex;
  align-items: center;
  gap: 4px;
}

.dot-separator {
  color: #cccccc;
}

.member-perk-chip {
  display: inline-flex;
  align-items: center;
  gap: 4px;
  font-size: 10px;
  font-weight: 600;
  color: #854d0e;
  background-color: #fef9c3;
  padding: 2px 6px;
  border-radius: 4px;
  border: 1px solid #fef08a;
  width: fit-content;
}

.chip-icon {
  width: 10px;
  height: 10px;
}

.item-stepper-price {
  display: flex;
  align-items: center;
  justify-content: space-between;
  margin-top: 4px;
}

.nights-stepper {
  display: inline-flex;
  align-items: center;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  overflow: hidden;
}

.stepper-btn {
  width: 22px;
  height: 22px;
  background-color: #fafafa;
  border: none;
  font-size: 12px;
  font-weight: 600;
  color: #333333;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
}

.stepper-btn:hover:not(:disabled) {
  background-color: #ebebeb;
}

.stepper-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}

.stepper-value {
  font-size: 11px;
  font-weight: 600;
  color: #111111;
  padding: 0 6px;
}

.item-price-wrap {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
}

.strikethrough-price {
  font-size: 10px;
  color: #999999;
  text-decoration: line-through;
}

.actual-price {
  font-size: 12px;
  font-weight: 600;
  color: #111111;
}

/* Footer Summary */
.drawer-footer {
  border-top: 1px solid #ebebeb;
  padding: 16px 20px;
  background-color: #fafafa;
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.loyalty-preview-banner {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 8px 12px;
  background-color: #ffffff;
  border: 1px solid #e5e5e5;
  border-radius: 8px;
}

.loyalty-preview-banner.is-guest {
  border-style: dashed;
}

.preview-icon-wrap {
  width: 24px;
  height: 24px;
  border-radius: 50%;
  background-color: #f4f4f5;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.preview-icon-wrap svg {
  width: 12px;
  height: 12px;
  color: #854d0e;
}

.loyalty-preview-banner.is-guest .preview-icon-wrap svg {
  color: #666666;
}

.preview-text {
  display: flex;
  flex-direction: column;
}

.preview-title {
  font-size: 11px;
  font-weight: 600;
  color: #111111;
}

.preview-subtitle {
  font-size: 10px;
  color: #717171;
}

/* Price Breakdown */
.price-breakdown {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.breakdown-row {
  display: flex;
  align-items: center;
  justify-content: space-between;
  font-size: 12px;
  color: #555555;
}

.breakdown-row.is-discount {
  color: #15803d;
}

.breakdown-divider {
  height: 1px;
  background-color: #e5e5e5;
  margin: 4px 0;
}

.breakdown-row.is-total {
  font-size: 14px;
  font-weight: 700;
  color: #111111;
}

.total-amount {
  font-size: 15px;
  font-weight: 700;
  color: #111111;
}

/* CTAs */
.drawer-actions {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 4px;
}

.checkout-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  width: 100%;
  padding: 12px 16px;
  background-color: #111111;
  color: #ffffff;
  border: 1px solid #111111;
  border-radius: 8px;
  font-size: 13px;
  font-weight: 600;
  cursor: pointer;
  box-shadow: none;
  transition: background-color 0.15s ease;
}

.checkout-btn:hover {
  background-color: #262626;
}

.checkout-btn svg {
  width: 14px;
  height: 14px;
}

.footer-aux-links {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 4px;
}

.clear-cart-btn,
.continue-btn {
  background: none;
  border: none;
  font-size: 11px;
  color: #717171;
  cursor: pointer;
  padding: 2px 4px;
}

.clear-cart-btn:hover {
  color: #dc2626;
  text-decoration: underline;
}

.continue-btn:hover {
  color: #111111;
  text-decoration: underline;
}
</style>
